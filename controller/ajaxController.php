<?php

class ajaxController
{
    /**
     * @var object A templating instance
     */
    private $templating;

    public function __construct(templating $templating)
    {
        $this->templating = $templating;
    }

    public function createAction()
    {
        $poll = array(
            'name' => null,
            'choices' => array(),
            'order' => count($_SESSION['polls']),
            'id' => count($_SESSION['polls']),
            'visible' => false,
        );

        if (isset($_POST['name'])) {
            $poll['name'] = $_POST['name'];
        }

        if (isset($_POST['visible'])) {
            $poll['visible'] = (bool) $_POST['visible'];
        }

        if (isset($_POST['choices'])) {
            $poll['choices'] = $_POST['choices'];
        }

        // store new poll
        $_SESSION['polls'][] = $poll;

        echo json_encode($poll);
    }

    public function editAction($id)
    {
        foreach($_SESSION['polls'] as $key => $poll) {
            if ($poll['id'] == $id) {
                break;
            }
        }

        $poll = $_SESSION['polls'][$key];

        if (isset($_POST['name'])) {
            $poll['name'] = $_POST['name'];
        }

        if (isset($_POST['visible'])) {
            $poll['visible'] = (bool) $_POST['visible'];
        }

        if (isset($_POST['choices'])) {
            $poll['choices'] = $_POST['choices'];
        }

        // save poll
        $_SESSION['polls'][$key] = $poll;

        if (isset($_POST['order'])) {
            $from = $_POST['order']['from'];
            $to = $_POST['order']['to'];

            // start of the move
            $moveStart = min($from, $to);

            // we calculate the number of steps
            $steps = abs($from - $to);

            // we calculate direction
            $dir = $from > $to ? 'left' : 'right';

            // we take all polls between $from and $to
            $pollsToMove = array_slice($_SESSION['polls'], $moveStart + ($dir == 'left') ? 0 : 1, $steps);

            // update poll
            $_SESSION['polls'][$key]['order'] = (int) $to;

            // now, we update all polls 'order'
            foreach ($pollsToMove as $_poll) {
                $_SESSION['polls'][array_search($_poll, $_SESSION['polls'])]['order'] = ($dir == 'left')
                    ? $_poll['order'] + 1
                    : $_poll['order'] - 1
                ;
            }
        }

        echo json_encode($_SESSION['polls'][$key]);
    }

    public function deleteAction($id)
    {
        foreach($_SESSION['polls'] as $key => $poll) {
            if ($poll['id'] == $id) {
                break;
            }
        }

        // remove poll
        unset($_SESSION['polls'][$key]);

        echo json_encode($poll);
    }
}
