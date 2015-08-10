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
<h1>Welcome to </h1>
<p>Hi <?= $first ?>,</p>
<p>
    This email to confirm the creation of your account on <?= $website ?>.<br>
    Please visit the following link to confirm your email address.<br>
    This link will expire in 24 hours<br>
</p>
<p>
    <?= $this->Html->link($url, $url) ?>
</p>
