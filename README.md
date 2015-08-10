[![Build Status](https://travis-ci.org/gintonicweb/users.svg)](https://travis-ci.org/gintonicweb/users)

# Users plugin for CakePHP

## Warning

Do not use, work in progress

## Install

```
composer require gintoniccms/users:dev-master
```

## Example config

```
$this->loadComponent('Auth', [
    'authorize' => 'Controller',
    'authenticate' => [
        'Form' => [
            'fields' => [
                'username' => 'email',
                'password' => 'password'
            ]
        ]
    ],
    'loginAction' => [
        'controller' => 'Users',
        'action' => 'signin',
        'plugin' => 'Users',
        'prefix' => false
    ],
    'loginRedirect' => [
        'controller' => 'Users',
        'action' => 'view',
        'plugin' => 'Users',
        'prefix' => false
    ],
    'logoutRedirect' => [
        'controller' => 'Pages',
        'action' => 'home',
    ],
    'unauthorizedRedirect' => [
        'controller' => 'Users',
        'action' => 'signin',
        'plugin' => 'Users',
        'prefix' => false
    ]
]);
```
