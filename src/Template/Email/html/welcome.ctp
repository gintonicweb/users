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
<html>
<head>
    <title><?= $this->fetch('title') ?></title>
</head>
<body>
    <?= $this->fetch('content') ?>
    <h1>Before we get started...</h1>
    <p>
        Please take a second to make sure we’ve got your email right.
    </p>
    <p>
        <?= $this->Html->link('Confirm your email', $url) ?>
    </p>
</body>
</html>

