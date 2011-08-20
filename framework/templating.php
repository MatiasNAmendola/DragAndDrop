<?php

class templating
{
    /**
     * @var string Absolute path to templates directory
     */
    protected $templateDirectory;

    public function __construct($templateDirectory)
    {
        $this->templateDirectory = $templateDirectory;
    }

    /**
     * Render a template
     *
     * Include a template, declare parameters in it and return template's content
     *
     * @param __templateName string The template filename (eg: my_page.php)
     * @param __templateParams array Variables that get passed to template
     */
    public function render($__templateName, $__templateParams = array())
    {
        $__templatePath = sprintf('%s/%s', $this->templateDirectory, $__templateName);

        if (!file_exists($__templatePath)) {
            throw new \InvalidArgumentException(sprintf('You have requested an invalid template \'%s\'', $__templateName));
        }

        ob_start();
        extract($__templateParams);
        include $__templatePath;

        return ob_get_clean();
    }
}
