<?php

use Cake\Routing\Router;

Router::scope('/', ['plugin' => 'Users'], function ($routes) {

    $routes->connect('/signin', ['controller' => 'Users', 'action' => 'signin']);
    $routes->connect('/signout', ['controller' => 'Users', 'action' => 'signout']);
    $routes->connect('/signup', ['controller' => 'Users', 'action' => 'signup']);
    $routes->connect('/users', ['controller' => 'Users']);
    $routes->connect('/users/:action/*', ['controller' => 'Users'], ['routeClass' => 'DashedRoute']);

    $routes->prefix('api', function ($routes) {
        $routes->extensions(['json','xml']);
        $routes->connect('/users', ['controller' => 'Users']);
        $routes->connect('/users/register', ['controller' => 'Users', 'action' => 'add']);
        $routes->connect('/users/:action/*', ['controller' => 'Users']);
        $routes->resources('Users.Users');
    });

});
