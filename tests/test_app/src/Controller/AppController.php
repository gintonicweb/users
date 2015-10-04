<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

class AppController extends Controller
{
    public function initialize()
    {
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'passwordHasher' => 'Bypass',
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password',
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'signin'
            ]
        ]);
        $this->Auth->allow(['signup', 'signin']);
        parent::initialize();
    }
}
