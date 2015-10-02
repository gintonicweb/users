<?php $website = \Cake\Routing\Router::url('/', true) ?>
<?php $url = \Cake\Routing\Router::url(
    [
        'plugin'=>'Users',
        'controller'=>'users',
        'action'=>'verify',
        $userId,
        $token
    ],
    true
)?>

<h1>Email verification</h1>
<p>Hi <?= $username ?>,</p>
<p>
    An email verification has been requested for your account on <?= $website ?>.<br>
    Please visit the following link to confirm your account.<br>
    The link will expire in 24 hours<br>
</p>
<p>
    <?= $this->Html->link($url, $url) ?>
</p>
