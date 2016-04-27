<?php
use Cake\Routing\Router;
$url = Router::url(
    [
        'controller'=>'users',
        'action'=>'verify',
        $token
    ],
    true
);
?>

Before we get started...

Please take a second to make sure we’ve got your email right.

By visiting the following link: <?= $url ?>
