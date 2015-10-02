<?php $website = \Cake\Routing\Router::url('/', true) ?>
<?php $url = \Cake\Routing\Router::url(
    [
        'plugin'=>'Users',
        'controller'=>'users',
        'action'=>'recover',
        $userId,
        $token
    ],
    true
)?>

<h1>Password Recovery</h1>
<p>Hi <?= $username ?>,</p>
<p>
A password recovery request has been requested for your account on <?= $website ?>.<br>
    Please visit the following link to reset your password.<br>
    The link will expire in 24 hours<br>
</p>
<p>
    <?= $this->Html->link($url, $url) ?>
</p>
