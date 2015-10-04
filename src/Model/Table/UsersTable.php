<?php
namespace Users\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
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
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('MessageReadStatuses', [
            'foreignKey' => 'user_id',
            'className' => 'Users.MessageReadStatuses'
        ]);
        $this->hasMany('Messages', [
            'foreignKey' => 'user_id',
            'className' => 'Users.Messages'
        ]);
        $this->belongsToMany('Threads', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'thread_id',
            'joinTable' => 'threads_users',
            'className' => 'Users.Threads'
        ]);
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
            ->requirePresence('password')
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

    public function beforeSave(Event $event, User $entity, ArrayObject $options)
    {
        $entity->token = md5(uniqid(rand(), true));
        if (empty($entity->username)) {
            $entity->username = $entity->email;
        }
    }
}
