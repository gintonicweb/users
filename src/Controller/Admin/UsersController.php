<?php
namespace Users\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Users Controller
 *
 * Handle the mechanics of logging users in, password management and
 * authentication. This base class is intended to stay as lean as possible while
 * being easily reusable from any application.
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
        $this->Cookie->config('path', '/');
        $this->Cookie->config(['httpOnly' => true]);
    }

    /**
     * TODO: blockquote
     */
    public function index()
    {
        $aros = TableRegistry::get('Aros');
        $users = $this->Users->find()
            ->contain(['Aros']);
        $users = $this->Users->bindRoles($this->paginate($users));
        $this->set('users', $users);
    }

    /**
     * TODO: blockquote
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $user = $this->Users->newEntity($this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->set(__('The user has been saved successfully.'));
                return $this->redirect([
                    'controller' => 'users',
                    'action' => 'index'
                ]);
            }
            $this->Flash->set(__('Unable to add user.'));
            $this->set('user', $user);
        }
    }

    /**
     * TODO: blockquote
     */
    public function edit($id)
    {
        $user = $this->Users->get($id)->accessible('password', true);
        if ($this->request->is(['post', 'put'])) {
            if ($this->request->data['pwd'] != 'dummy') {
                $this->request->data['password'] = $this->request->data['pwd'];
            }
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->set(__('Account updated successfully'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->set(__('Error updating the account'));
        }
        $this->set(compact('user'));
    }

    /**
     * TODO: blockquote
     */
    public function delete($id)
    {
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->set(__('Users deleted'));
            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->set(__('Error deleting user'));
        return $this->redirect(['action' => 'index']);
    }
}
