<?php

class indexController
{
    /**
     * @var object A templating instance
     */
    private $templating;

    public function __construct(templating $templating)
    {
        $this->templating = $templating;
    }

    public function indexAction()
    {
        $polls = $_SESSION['polls'];

        return $this->templating->render('index.php', array(
            'polls' => $polls,
        ));
    }
}
