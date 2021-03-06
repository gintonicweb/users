<?php
namespace Users\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;

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

    /**
     * Before filter method
     *
     * @param \Cake\Event\Event $event the event
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Crud->addListener('Users.Users', ['mailer' => 'Users.User']);
        $this->Crud->mapAction('signup', [
            'className' => 'CrudUsers.Register',
            'view' => 'Users.signup',
            'saveOptions' => [
                'fieldList' => ['username', 'email', 'password']
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
            'className' => 'CrudUsers.Login',
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

        $this->Crud->mapAction('sendRecovery', [
            'className' => 'CrudUsers.ForgotPassword',
        ]);

        $this->Crud->mapAction('changePassword', [
            'className' => 'CrudUsers.ResetPassword',
            'findMethod' => 'token',
        ]);

        $this->Crud->mapAction('verify', [
            'className' => 'CrudUsers.Verify',
            'findMethod' => 'token',
        ]);
    }


    /**
     * Edit method
     *
     * @return void
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
            $user = $this->Users->patchEntity($user, $this->request->data);
            $this->Users->save($user);
        }
        $this->set(compact('user'));
    }
}
