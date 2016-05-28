<?php

namespace Users\Listener;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Crud\Listener\BaseListener;
use JWT;

class UsersListener extends BaseListener
{

    use MailerAwareTrait;

    protected $_defaultConfig = [
        'mailer' => 'Users.User',
    ];

    /**
     * Callbacks definition
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Crud.beforeFilter' => 'beforeFilter',
            'Crud.afterRegister' => 'afterRegister',
            'Crud.afterLogin' => 'afterLogin',
            'Crud.afterLogout' => 'afterLogout',
            'Crud.afterForgotPassword' => 'afterForgotPassowrd',
            'Crud.beforeSave' => 'beforeSave',
            'Crud.verifyToken' => 'verifyToken',
        ];
    }

    /**
     * After Forgot Password
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function afterForgotPassword(Event $event)
    {
        $this->_sendToken($event, 'forgotPassword');
    }

    /**
     * Before Filter
     *
     * Set the cookie and authenticate users, or invalidate the cookie.
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        if (!$this->_controller()->Auth->user() &&
            $this->_controller()->Cookie->read('CookieAuth')) {

            $user = $this->_controller()->Auth->identify();
            if ($user) {
                $this->_controller()->Auth->setUser($user);
            } else {
                $this->_controller()->Cookie->delete('CookieAuth');
            }
        }
    }

    /**
     * After Register
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function afterRegister(Event $event)
    {
        if ($event->subject->success) {
            $this->_setUser($event->subject->entity->toArray());
            $this->_setJwt($event->subject->entity->id);
            $this->_sendToken($event, 'register');
        }
    }

    /**
     * After Login
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function afterLogin(Event $event)
    {
        if ($event->subject->success) {
            $this->_setUser($event->subject->user);
            $this->_setJwt($event->subject->user['id']);
        }
    }

    /**
     * After Logout
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function afterLogout(Event $event)
    {
        $this->_controller()->Cookie->delete('Jwt');
        $this->_controller()->Cookie->delete('CookieAuth');
    }

    /**
     * Before Save
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function beforeSave(Event $event)
    {
        if (isset($this->_controller()->request->data['password'])) {
            $password = $this->_controller()->request->data['password'];
            $event->subject->entity['password'] = $password;
        }
    }

    /**
     * Before Verify
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function verifyToken(Event $event)
    {
        $event->subject->verified = TableRegistry::get('Muffin/Tokenize.Tokens')
            ->verify($event->subject->token);
    }

    /**
     * Send Token
     *
     * @param \Cake\Event\Event $event Event
     * @param string $mailerName Name of the mailer config to use
     * @return void
     */
    protected function _sendToken(Event $event, $mailerName)
    {
        $table = TableRegistry::get($this->_controller()->modelClass);
        $token = $table->tokenize($event->subject->entity->id);

        if ($this->config('mailer')) {
            $test = $this->getMailer($this->config('mailer'))->send($mailerName, [
                $event->subject->entity->toArray(),
                $token,
            ]);
        }
    }

    /**
     * Set User
     *
     * @param array $user User details
     * @return void
     */
    protected function _setUser(array $user)
    {
        if (isset($this->_controller()->request->data['remember'])) {
            $user['password'] = $this->_controller()->request->data['password'];
            $this->_controller()->Cookie->write('CookieAuth', $user);
        }
        $this->_controller()->Auth->setUser($user);
    }

    /**
     * Set Jwt token to a cookie
     *
     * @param int $userId User id
     * @return void
     */
    protected function _setJwt($userId)
    {
        $cookie = !empty($this->_controller()->Cookie->read('CookieAuth'));
        $expiration = $this->_controller()->Cookie->configKey('CookieAuth')['expires'];

        $this->_controller()->Cookie->configKey('Jwt', [
            'encryption' => false,
            'expires' => $cookie ? $expiration : 0,
        ]);
        $token = ['id' => $userId, 'exp' => time() + strtotime($expiration)];
        $jwt = JWT::encode($token, Security::salt());
        $this->_controller()->Cookie->write('Jwt', $jwt);
    }
}
