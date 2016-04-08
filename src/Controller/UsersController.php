<?php
namespace Users\Controller;

use App\Controller\AppController;
use Cake\Collection\Collection;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Security;
use JWT;

/**
 * Users Controller
 *
 * Handle the mechanics of logging users in, password management and
 * authentication. This base class is intended to stay as lean as possible while
 * being easily reusable from any application.
 *
 * @property \Crud\Controller\Component\CrudComponent Crud
 * @property \Users\Model\Table\UsersTable Users
 */
class UsersController extends AppController
{
    /**
     * Setting up the cookie
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cookie');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Crud->mapAction('signup', [
            'className' => 'CrudUsers.register',
            'view' => 'Users.signup',
            'saveOptions' => [
                'fieldList' => [
                    'username',
                    'email',
                    'password'
                ]
            ],
            'messages' => [
                'success' => [
                    'text' => __('Please check your e-mail to validate your account')
                ],
                'error' => [
                    'text' => __('An error occurred while creating the account')
                ]
            ],
        ]);

        $this->Crud->mapAction('signin', [
            'className' => 'CrudUsers.login',
            'view' => 'Users.signin',
            'messages' => [
                'success' => [
                    'text' => __('Login successful. Please validate your email address.')
                ],
                'error' => [
                    'text' => __('Your username or password is incorrect.')
                ]
            ],
        ]);

        $this->Crud->mapAction('signout', [
            'className' => 'CrudUsers.Logout',
            'messages' => [
                'success' => [
                    'text' => __('You are now signed out.')
                ],
            ],
        ]);
    }


    /**
     * Authenticate users
     *
     * @param int $userId id of the authentified user
     * @param string $expires hoq long the Jwt cookie should last
     * @return void
     */
    protected function _setJwt($userId, $expires = null)
    {
        if ($expires === null) {
            $expires = $this->Cookie->configKey('User')['expires'];
        }
        $this->Cookie->configKey('Jwt', [
            'encryption' => false,
            'expires' => $expires,
        ]);
        $token = [
            'id' => $userId,
            'exp' => time() + strtotime($this->Cookie->configKey('User')['expires'])
        ];
        $this->Cookie->write('Jwt', JWT::encode($token, Security::salt()));
    }


    /**
     * Edit method
     *
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $id = $this->Auth->user('id');
        $user = $this->Users->find()->where(['id' => $id])->first();
        if (!$user) {
            throw new NotFoundException('User could not be found');
        }
            
        $user->accessible('password', true);
        if ($this->request->is(['post', 'put'])) {
            $this->_update($user);
        }
        $this->set(compact('user'));
    }

    /**
     * Verify that an email address truly belongs to a given user. Users end
     * up here by following a link they get via email.
     *
     * @param int $id The user id being verified
     * @param string $token A secret token sent in the link
     * @return void|\Cake\Network\Response
     */
    public function verify($id, $token)
    {
        $user = $this->Users->find()
            ->where(['id' => $id, 'token' => $token])
            ->first();

        if (!$user) {
            throw new NotFoundException(__('Error while validating email'));
        }

        $user->verified = true;
        $this->Users->save($user);
        $this->Auth->setUser($user->toArray());
        $this->Flash->set(__('Email address validated successfuly'));
        return $this->redirect($this->Auth->redirectUrl());
    }

    /**
     * Anytime a user needs to be saved
     *
     * @param \Users\Model\Entity\User $user user entity
     * @return void|\Cake\Network\Response
     */
    protected function _update($user)
    {
        $user = $this->Users->patchEntity($user, $this->request->data);
        if ($this->Users->save($user)) {
            $this->Auth->setUser($user->toArray());
            $this->Flash->set(__('Password has been updated successfully.'));
            return $this->redirect($this->Auth->redirectUrl());
        } else {
            $this->Flash->set(__('Error while resetting password'));
        }
    }

    /**
     * If a user hasn't verified his email and has lost the initial verification
     * mail he can request a new verification mail by visiting this action
     *
     * @return void
     */
    public function sendVerification()
    {
        $this->render(false);
        $id = $this->Auth->user('id');
        $user = $this->Users->find()->where(['id' => $id])->first();
        if (!$user) {
            throw new NotFoundException('User could not be found');
        }
        $user->dirty('token', true);
        $user = $this->Users->save($user);
        if ($user) {
            $event = new Event('Users.sendVerification', $this, ['user' => $user]);
            $this->eventManager()->dispatch($event);
        }
    }

    /**
     * Allows users to request an e-mail for password recovery token and
     * instructions
     *
     * @return void|\Cake\Network\Response
     */
    public function sendRecovery()
    {
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->findByEmail($this->request->data['email'])->first();
            $user->dirty('token', true);
            if ($user) {
                $user = $this->Users->save($user);
                if ($user) {
                    $event = new Event('Users.sendRecovery', $this, ['user' => $user]);
                    $this->eventManager()->dispatch($event);
                }
            }
            $this->Flash->set(__('An email was sent with password recovery instructions.'));
            return $this->redirect($this->Auth->redirectUrl());
        }
    }
}
