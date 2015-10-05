<?php
namespace Users\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\Time;
use Cake\Network\Email\Email;
use Cake\ORM\Entity;

/**
 * User Entity.
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'verified' => false,
        'token' => false,
        'password' => false,
    ];
    protected $_hidden = ['password', 'token', 'token_creation'];

    /**
     * Take plaintext password and return valid Hash of that password.
     *
     * @param string $password plaintext password string
     * @return string Hash password string
     */
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }
}
