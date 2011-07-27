<?php

class application
{
    /**
     * @var string Absolute path to controllers directory
     */
    private $controllerDirectory;

    /**
     * @var object A templating instance
     */
    private $templating;

    /**
     * @var object An array of routes
     *
     * A route look like this:
     *  {
     *      'pattern' : '/folder/page',
     *      'action' : 'index:index',
     *      'method' : 'GET',
     *  }
     */
    private $routes;

    /**
     * @param directory string Base directory who contains controllers and templates
     */
    public function __construct($directory)
    {
        $routingFile = $directory.'/routes.php';
        $routes = include $routingFile;

        if (!file_exists($routingFile) || !is_array($routes)) {
            throw new \ErrorException('routing file is invalid');
        }

        $this->routes = $routes;
        $this->controllerDirectory = $directory.'/controller';
        $this->templating = new templating($directory.'/template');
    }

    /**
     * Handle the request and convert it to a response
     *
     * 1. url is parsed to find a route
     * 2. route controller is instanciated
     * 3. controller action is called and return response content
     */
    public function run()
    {
        list(list($controllerName, $actionName), $actionParams) = $this->matchUrl($_SERVER['PATH_INFO']);

        $controller = $this->getController($controllerName);

        echo $this->callAction($controller, $actionName, $actionParams);
    }

    /**
     * Check that a url match a route
     *
     * @param url string    Path info
     */
    private function matchUrl($url)
    {
        $routeFound = false;

        foreach ($this->routes as $route) {
            $pattern = sprintf('#^%s$#', $route['pattern']);
            $method = $_SERVER['REQUEST_METHOD'];
            if (preg_match($pattern, $url, $matches) && $route['method'] == $method) {
                $routeFound = $route;
                break;
            }
        }

        if (false === $routeFound) {
            throw new \ErrorException('404 : route not found');
        }

        // sanitize parameters
        foreach ($matches as $i => $match) {
            if (is_int($i)) unset($matches[$i]);
        }

        return array(explode(':', $route['action']), $matches);
    }

    /**
     * Get a controller instance depending on a name
     *
     * @param controllerName string     the controller name (eg: ajax)
     */
    private function getController($controllerName)
    {
        $controllerClass = sprintf('%sController', $controllerName);
        $controllerPath = sprintf('%s/%s.php', $this->controllerDirectory, $controllerClass);

        if (!file_exists($controllerPath)) {
            throw new \InvalidArgumentException(sprintf('You have requested an invalid controller \'%s\'', $controllerClass));
        }

        require $controllerPath;
        return new $controllerClass($this->templating);
    }

    /**
     * Call an action on a controller
     *
     * @param controller object     an instance of the controller
     * @param actionName string     the action name (eg: homepage)
     */
    private function callAction($controller, $action, $params = array())
    {
        $method = sprintf('%sAction', $action);
        $controllerRefl = new \ReflectionClass($controller);

        if (!$controllerRefl->hasMethod($method)) {
            throw new \InvalidArgumentException(sprintf('action \'%s\' do not exists', $action));
        }

        $reflMethod = $controllerRefl->getMethod($method);

        // if a $_GET['x'] parameter is found with and the action method accepts a $x argument
        // we add the GET parameter to the action call
        $actionParams = array();
        foreach($reflMethod->getParameters() as $reflParam) {
            $actionParams[] = isset($params[$reflParam->name]) ? $params[$reflParam->name] : null;
        }

        return $reflMethod->invokeArgs($controller, $actionParams);
    }
}
