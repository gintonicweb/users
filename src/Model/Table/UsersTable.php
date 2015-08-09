<?php
namespace Users\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Users\Model\Entity\User;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $Aros
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
        $this->displayField('email');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->hasMany('Acl.Aros', [
            'conditions' => ['Aros.model' => 'Users'],
            'foreignKey' => 'foreign_key'
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
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');

        $validator
            ->requirePresence('first', 'create')
            ->notEmpty('first');

        $validator
            ->requirePresence('last', 'create')
            ->notEmpty('last');

        $validator
            ->add('verified', 'valid', ['rule' => 'boolean'])
            ->requirePresence('verified', 'create')
            ->notEmpty('verified');

        $validator
            ->requirePresence('token', 'create')
            ->notEmpty('token');

        $validator
            ->add('token_creation', 'valid', ['rule' => 'datetime'])
            ->requirePresence('token_creation', 'create')
            ->notEmpty('token_creation');

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
        return $rules;
    }
}
