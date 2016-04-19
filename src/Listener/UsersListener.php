<?php

namespace Users\Listener;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Crud\Listener\BaseListener;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use JWT;

class UsersListener extends BaseListener
{
    /**
     * Callbacks definition
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Crud.afterRegister' => 'register',
            'Crud.afterLogin' => 'login',
            'Crud.afterLogout' => 'logout',
            'Crud.afterForgotPassword' => 'createToken',
        ];
    }

    public function register(Event $event)
    {
        $event->subject->user = $event->subject->entity->toArray();
        $this->login($event);
    }

    public function login(Event $event)
    {
        if ($event->subject->success) {
            $this->_setUser($event->subject->user);
            $this->_setJwt($event->subject->user['id']);
        }
    }

    public function logout(Event $event)
    {
        $this->_controller()->Cookie->delete('Jwt');
        $this->_controller()->Cookie->delete('RememberMe');
    }

    public function createToken(Event $event)
    {
        $event->subject->entity->dirty('token', true);
        $table = TableRegistry::get($this->_controller()->modelClass);
        $table->save($event->subject->entity);
    }

    protected function _setUser($entity)
    {
        if (isset($this->_controller()->request->data['remember'])) {
            $entity['password'] = $this->_controller()->request->data['password'];
            $this->_controller()->Cookie->write('RememberMe', $entity);
        }
        $this->_controller()->Auth->setUser($entity);
    }

    protected function _setJwt($userId)
    {
        $cookie = !empty($this->_controller()->Cookie->read('RememberMe'));
        $expiration = $this->_controller()->Cookie->configKey('RememberMe')['expires'];

        $this->_controller()->Cookie->configKey('Jwt', [
            'encryption' => false,
            'expires' => $cookie ? $expiration : 0,
        ]);
        $token = ['id' => $userId, 'exp' => time() + strtotime($expiration)];
        $jwt = JWT::encode($token, Security::salt());
        $this->_controller()->Cookie->write('Jwt', $jwt);
    }
}
