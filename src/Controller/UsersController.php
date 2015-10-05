<?php
namespace Users\Controller;

use App\Controller\AppController;
use Cake\Collection\Collection;
use Cake\Event\Event;
use Cake\Mailer\MailerAwareTrait;
use Cake\Network\Exception\NotFoundException;

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
    }

    /**
     * Users registration
     *
     * @return void
     */
    public function signup()
    {
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->newEntity()->accessible('password', true);
            $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->set(__('Please check your e-mail to validate your account'));
                $this->Auth->setUser($user->toArray());
                //$this->getMailer('Users.User')->send('signup', [$user]);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                return false;
                $this->Flash->error(__('An error occured while creating the account'));
            }
        }
    }

    /**
     * Authenticate users
     *
     * @return void
     */
    public function signin()
    {
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                if (isset($this->request->data['remember'])) {
                    $this->Cookie->write('User', $user);
                }
                if (!$user['verified']) {
                    $this->Flash->set(__('Login successful. Please validate your email address.'));
                }
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->set(__('Your username or password is incorrect.'));
            return false;
        }
    }

    /**
     * Un-authenticate users and remove data from session and cookie
     *
     * @return void
     */
    public function signout()
    {
        $this->request->session()->destroy();
        $this->Cookie->delete('User');
        $this->Flash->set(__('You are now signed out.'));
        return $this->redirect($this->Auth->logout());
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
