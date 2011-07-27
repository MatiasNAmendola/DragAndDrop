<?php

return array(
    array(
        'pattern' => '/',
        'action' => 'index:index',
        'method' => 'GET',
    ),
    array(
        'pattern' => '/ajax/create',
        'action' => 'ajax:create',
        'method' => 'POST',
    ),
    array(
        'pattern' => '/ajax/delete/(?P<id>.+)',
        'action' => 'ajax:delete',
        'method' => 'POST',
    ),
    array(
        'pattern' => '/ajax/edit/(?P<id>.+)',
        'action' => 'ajax:edit',
        'method' => 'POST',
    ),
);
