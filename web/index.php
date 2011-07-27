<?php

require __DIR__.'/../Sminny/src/application.php';
require __DIR__.'/../Sminny/src/templating.php';


// as we store polls in the session, we start the session
session_start();

// we can reset data with a url like /index.php/?reset=true
$reset = isset($_GET['reset']);

// if we have not any data, we load them
if ($reset || !isset($_SESSION['polls'])) {
    $_SESSION['polls'] = require __DIR__.'/../polls.php';
}

// remove refresh parameter
if (isset($_GET['reset'])) {
    header(sprintf('Location: %s', $_SERVER['HTTP_REFERER']));
    exit;
}

// we want to sort polls by 'order'
usort($_SESSION['polls'], function($a, $b) {
    if ($a['order'] == $b['order']) {
        return 0;
    }
    return $a['order'] > $b['order'] ? 1 : -1;
});

// we start the application
$app = new application(__DIR__.'/..');
$app->run();
