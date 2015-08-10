<?php
namespace Users\Controller;

use App\Controller\AppController;
use Cake\Collection\Collection;
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
        $this->Cookie->config('path', '/');
        $this->Cookie->config(['httpOnly' => true]);
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
        $this->Auth->allow(['signin', 'signup', 'verify', 'recover', 'sendRecovery']);

        // The following actions should not be available to authenticated users
        $unAuthActions = ['signin', 'signup', 'recover'];
        $unAuth = in_array($this->request->params['action'], $unAuthActions);
        if ($this->Auth->user() && $unAuth) {
            return $this->redirect($this->Auth->redirectUrl());
        }

        parent::beforeFilter($event);
    }

    /**
     * Authenticated users are allowed to access everything in this controller
     *
     * @param array|null $user The user to check the authorization of.
     * @return bool True if $user is authorized, otherwise false
     */
    public function isAuthorized($user = null)
    {
        if (!empty($user)) {
            return true;
        }
        return parent::isAuthorized($user);
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
        if (empty($id) && $this->request->session()->read('Auth.User.id')) {
            $id = $this->request->session()->read('Auth.User.id');
        }
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
        $id = $this->request->session()->read('Auth.User.id');
        $user = $this->Users->get($id)->accessible('password', true);

        if ($this->request->is(['post', 'put'])) {
            if ($this->request->data['pwd'] != 'dummy') {
                $this->request->data['password'] = $this->request->data['pwd'];
            }
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->set(__('Account updated successfully'));
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->set(__('Error updating the account'));
        }
        $this->set(compact('user'));
    }

    /**
     * Users registration
     */
    public function signup()
    {
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->newEntity()->accessible('password', true);
            $this->Users->patchEntity($user, $this->request->data);
            $user->updateToken();
            if ($this->Users->save($user)) {
                $this->Flash->set(__('Please check your e-mail to validate your account'));
                $this->Auth->setUser($user->toArray());
                $this->getMailer('User')->send('signup', [$user]);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error(__('An error occured while creating the account'));
                return;
            }
        }
    }

    /**
     * Authenticate users
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
        }
    }

    /**
     * Un-authenticate users and remove data from session and cookie
     */
    public function signout()
    {
        $this->request->session()->destroy();
        $this->Flash->set(__('You are now signed out.'));
        $this->Cookie->delete('User');
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Verify that an email address truly belongs to a given user. Users end
     * up here by following a link they get via email.
     *
     * @param int $id The user id being verified
     * @param string $token A secret token sent in the link
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
     */
    public function recover($id, $token)
    {
        $user = $this->Users->get($id)->accessible('password', true);
        if (!$user->verify($token)) {
            $this->Flash->set(__('Recovery token has expired'));
            return $this->redirect([
                'controller' => 'Users',
                'action' => 'sendRecovery',
                'plugin' => 'GintonicCMS'
            ]);
        }
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Auth->setUser($user->toArray());
                $this->Flash->set(__('Password has been updated successfully.'));
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->set(__('Error while resetting password'));
            }
        }
        $this->set(compact('id', 'token'));
    }

    /**
     * If a user hasn't verified his email and has lost the initial verification
     * mail he can request a new verification mail by visiting this action
     */
    public function sendVerification()
    {
        $userId = $this->request->session()->read('Auth.User.id');
        $user = $this->Users->get($userId);
        $this->getMailer('User')->send('verification', [$user]);
    }

    /**
     * Allows users to request an e-mail for password recovery token and
     * instructions
     */
    public function sendRecovery()
    {
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->findByEmail($this->request->data['email'])->first();

            if (empty($user)) {
                $this->Flash->set(__('No matching email address found.'));
            } else {
                // TODO: write a test that vaidates that the token is updated
                // careful: this is a guarded field
                $user->updateToken();
                if ($this->Users->save($user)) {
                    $this->getMailer('User')->send('recovery', [$user]);
                    $this->Flash->set(__('An email was sent with password recovery instructions.'));
                    return $this->redirect($this->Auth->redirectUrl());
                }
            }
        }
    }
}
