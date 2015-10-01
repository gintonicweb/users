<?php
namespace Users\Controller;

use App\Controller\AppController;
use Cake\Collection\Collection;
use Cake\Core\App;
use Cake\Event\Event;
use Cake\Mailer\MailerAwareTrait;

/**
 * Users Controller
 *
 * Handle the mechanics of logging users in, password management and
 * authentication. This base class is intended to stay as lean as possible while
 * being easily reusable from any application.
 */
class UsersController extends AppController
{
    use MailerAwareTrait;

    /**
     * Setting up the cookie
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cookie');
        $this->loadComponent('Auth');
    }

    /**
     * Defines the methods that should be allowed for non authenticated users
     * and the ones that shouldn't be accessed by authenticated users. Also
     * define which actions should use the bare layout
     *
     * @param Event $event An Event instance
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        $this->Crud->mapAction('signin', 'CrudUsers.Login');
        $this->Crud->mapAction('signout', 'CrudUsers.Logout');
        $this->Crud->mapAction('signup', 'CrudUsers.Register');
        $this->Crud->addListener('Users.Users');

        parent::beforeFilter($event);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $id = $id != null ? $id : $this->Auth->user('id');
        $user = $this->Users->get($id);
        $this->set(compact('user'));
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
        $user = $this->Users->get($id)->accessible('password', true);
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
        $user = $this->Users->get($id);
        if ($user->verified || $user->verify($token)) {
            $this->Users->save($user);
            $this->Flash->set(__('Email address validated successfuly'));
        } else {
            $this->Flash->set(__('Error while validating email'));
        }
        return $this->redirect($this->Auth->redirectUrl());
    }

    /**
     * Allow users to reset their passwords without being logged in. Users end
     * up here by following a link they get via email.
     *
     * @param int $id The user id being verified
     * @param string $token A secret token sent in the link
     * @return void|\Cake\Network\Response
     */
    public function recover($id, $token)
    {
        $user = $this->Users->get($id)->accessible('password', true);
        if (!$user->verify($token)) {
            $this->Flash->set(__('Recovery token invalid'));
            return $this->redirect([
                'controller' => 'Users',
                'action' => 'sendRecovery',
                'plugin' => 'Users'
            ]);
        }
        if ($this->request->is(['post', 'put'])) {
            $this->_update($user);
        }
        $this->set(compact('id', 'token'));
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
        $userId = $this->request->session()->read('Auth.User.id');
        $user = $this->Users->get($userId);
        //$this->getMailer('User')->send('verification', [$user]);
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

            if (empty($user)) {
                $this->Flash->set(__('No matching email address found.'));
            } elseif ($this->Users->save($user)) {
                //$this->getMailer('Users.User')->send('recovery', [$user]);
                $this->Flash->set(__('An email was sent with password recovery instructions.'));
                return $this->redirect($this->Auth->redirectUrl());
            }
        }
    }
}
