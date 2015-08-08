<?php
/**
 * GintonicCMS : Full Stack Content Management System (http://gintoniccms.com)
 * Copyright (c) Philippe Lafrance (http://phillafrance.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Philippe Lafrance (http://phillafrance.com)
 * @link          http://gintoniccms.com GintonicCMS Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace GintonicCMS\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use GintonicCMS\Model\Entity\User;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $MessageReadStatuses
 * @property \Cake\ORM\Association\HasMany $Messages
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
        $this->table('users');
        $this->displayField('email');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->hasMany('MessageReadStatuses', [
            'foreignKey' => 'user_id',
            'className' => 'GintonicCMS.MessageReadStatuses'
        ]);
        $this->hasMany('Messages', [
            'foreignKey' => 'user_id',
            'className' => 'GintonicCMS.Messages'
        ]);
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

    /**
     * TODO: docblock
     */
    public function bindRoles(ResultSet $users)
    {
        // Avoid loading the association and condition in current model by using
        // the aros directly from the TableRegistry
        $aros = TableRegistry::get('Aros');

        return $users->map(function ($row) use (&$aros) {
            $row->roles = [];

            foreach ($row->aros as $aro) {
                $roles = $aros
                    ->find('path', ['for' => $aro['id']])
                    ->select(['id'])
                    ->distinct();
                $roleGroup = $aros->find()
                    ->where([
                        'Aros.id IN' => $roles,
                    ])
                    ->where(['alias IS NOT' => null])
                    ->hydrate(false);
                foreach ($roleGroup as $role) {
                    $row->roles[] = $role;
                }
            }

            return $row;
        });
    }
}
