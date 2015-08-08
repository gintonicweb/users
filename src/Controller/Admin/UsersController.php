<?php
namespace GintonicCMS\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class UsersController extends AppController
{
    public $paginate = [
        'limit' => 25,
        'order' => [
            'Users.created' => 'desc'
        ]
    ];

    /**
     * TODO: blockquote
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
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
    public function permissions()
    {
        $aros = TableRegistry::get('Aros');
        $roles = $aros->find()
            ->where(['alias IS NOT' => null])
            ->find('threaded')
            ->toArray();

        $acos = TableRegistry::get('Acos');
        $permissions = $acos->find()
            ->where(['alias IS NOT' => null])
            ->find('threaded')
            ->toArray();
        $this->set(compact('roles', 'permissions'));
    }

    /**
     * TODO: blockquote
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $user = $this->Users->newEntity($this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->set(__('The user has been saved successfully.'), [
                    'element' => 'GintonicCMS.alert',
                    'params' => ['class' => 'alert-success']
                ]);
                return $this->redirect([
                    'controller' => 'users',
                    'action' => 'index'
                ]);
            }
            $this->Flash->set(__('Unable to add user.'), [
                'element' => 'GintonicCMS.alert',
                'params' => ['class' => 'alert-danger']
            ]);
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
                $this->Flash->set(__('Account updated successfully'), [
                    'element' => 'GintonicCMS.alert',
                    'params' => ['class' => 'alert-success']
                ]);
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->set(__('Error updating the account'), [
                'element' => 'GintonicCMS.alert',
                'params' => ['class' => 'alert-danger']
            ]);
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
            $this->Flash->set(__('Users deleted'), [
                'element' => 'GintonicCMS.alert',
                'params' => ['class' => 'alert-success']
            ]);
            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->set(__('Error deleting user'), [
            'element' => 'GintonicCMS.alert',
            'params' => ['class' => 'alert-danger']
        ]);
        return $this->redirect(['action' => 'index']);
    }
}
