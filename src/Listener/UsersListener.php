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
            'Crud.beforeSave' => 'beforeSave',
            'Crud.beforeVerify' => 'beforeVerify',
        ];
    }

    public function register(Event $event)
    {
        if ($event->subject->success) {
            $this->_setUser($event->subject->entity->toArray());
            $this->_setJwt($event->subject->entity->id);
            $this->createToken($event);
        }
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
        $table = TableRegistry::get($this->_controller()->modelClass);
        $table->tokenize($event->subject->entity->id);
    }

    public function beforeSave(Event $event)
    {
        if (isset($this->_controller()->request->data['password'])) {
            $password = $this->_controller()->request->data['password'];
            $event->subject->entity['password'] = $password;
        }
    }

    public function beforeVerify(Event $event)
    {
        $table = TableRegistry::get($this->_controller()->modelClass);
        $token = $this->_controller->request->query['token'];
        $event->subject->query = $event->subject->query
            ->matching('Tokens', function ($q) use ($token){
                return $q->where(['Tokens.token' => $token]);
            });

        return TableRegistry::get('Muffin/Tokenize.Tokens')->verify($token);
    }

    protected function _setUser(array $user)
    {
        if (isset($this->_controller()->request->data['remember'])) {
            $user['password'] = $this->_controller()->request->data['password'];
            $this->_controller()->Cookie->write('RememberMe', $user);
        }
        $this->_controller()->Auth->setUser($user);
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
