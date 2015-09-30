<?php
namespace Users\Listener;

use Cake\Event\Event;
use Crud\Listener\BaseListener;

class UsersListener extends BaseListener
{

    /**
     * Returns a list of all events that will fire in the controller during its lifecycle.
     * You can override this function to add your own listener callbacks
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Crud.afterLogin' => ['callable' => 'afterLogin'],
            'Crud.afterLogout' => ['callable' => 'afterLogout'],
            'Crud.beforeRegister' => ['callable' => 'beforeRegister'],
            'Crud.afterRegister' => ['callable' => 'afterRegister'],
        ];
    }

    /**
     * Allows the password to be set on registration
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function beforeRegister(Event $event)
    {
        $event->subject->entity->accessible('password', true);
        $this->_table()->patchEntity(
            $event->subject->entity,
            $this->_controller()->request->data
        );
    }
    /**
     * Logs the newly registered user in
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function afterRegister(Event $event)
    {
        $user = $event->subject->entity;
        $this->_controller()->Auth->setUser($user->toArray());
        //$this->_controller()->getMailer('Users.User')->send('signup', [$user]);
    }

    /**
     * Sets the cookie if the rememberme option is checked
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function afterLogin(Event $event)
    {
        if (isset($this->_controller()->request->data['remember'])) {
            $this->_controller()->Cookie->write('User', $user);
        }
    }

    /**
     * Deletes the cookie after logout
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function afterLogout(Event $event)
    {
        $this->_controller()->Cookie->delete('User');
        $this->_controller()->request->session()->destroy();
    }
}
