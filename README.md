[![Build Status](https://travis-ci.org/gintonicweb/users.svg)](https://travis-ci.org/gintonicweb/users)
[![Coverage Status](https://coveralls.io/repos/gintonicweb/users/badge.svg?branch=master&service=github)](https://coveralls.io/github/gintonicweb/users?branch=master)

## Warning

Do not use, work in progress

# Users plugin for CakePHP

Based on `friendsofcake/authorize`. Support for signup, sigin, signout, register, password recovery and email verification, cookie and token authentication.

## Install instructions

via composer:
```
composer require gintoniccms/users:dev-master
```

run the migration
```
bin/cake migrations migrate -p Users
```

in bootstrap.php
```
Plugin::load('Users', ['routes' => true, 'bootstrap' => 'true']);
```

Add the following auth config to your AppController
```
$this->loadComponent('Auth', [
    'authenticate' => [
        AuthComponent::ALL => [
            'fields' => [
                'username' => 'email',
                'password' => 'password'
            ],
            'userModel' => 'Users.Users',
        ],
        'FOC/Authenticate.Cookie',
        'FOC/Authenticate.MultiColumn' => [
            'columns' => ['email'],
        ]
    ],
    'loginAction' => [
        'controller' => 'Users',
        'action' => 'signin',
        'plugin' => 'Users',
        'prefix' => false
    ],
]);
```

Allow the desired actions to your AppController
```
public function beforeFilter(Event $event)
{
    if ($this->request->params['controller'] == 'Pages') {
        $this->Auth->allow(['signup', 'signin', 'verify', 'sendRecovery']);
    }
}
```
