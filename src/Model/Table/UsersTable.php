<?php
namespace Users\Model\Table;

use ArrayObject;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;
use Users\Model\Entity\User;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $MessageReadStatuses
 * @property \Cake\ORM\Association\HasMany $Messages
 * @property \Cake\ORM\Association\BelongsToMany $Threads
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('users');
        $this->displayField('username');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
        if (Plugin::loaded('Search')) {
            $this->addBehavior('Search.Search');
        }
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->add('email', 'valid', ['rule' => 'email'])
            ->requirePresence('email')
            ->notEmpty('email');

        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->isUnique(['username']));
        return $rules;
    }

    /**
     * Creates a new token if it is marked as empty and use email as the
     * username if username is blank
     *
     * @param \Cake\Event\Event $event Event instance
     * @param \Users\Model\Entity\User $entity User being saved
     * @param ArrayObject $options option
     * @return void
     */
    public function beforeSave(Event $event, User $entity, ArrayObject $options)
    {
        $uuid = md5(uniqid(rand(), true));
        $entity->token = $entity->dirty('token') ? $uuid : false;

        if (empty($entity->username)) {
            $entity->username = $entity->email;
        }
    }

    /**
     * Allows to search users by partial username
     * @return \Search\Manager
     */
    public function searchConfiguration()
    {
        $search = new Manager($this);
        $search->like('username', [
            'before' => true,
            'after' => true,
            'field' => [$this->aliasField('username')]
        ]);
        return $search;
    }
}
