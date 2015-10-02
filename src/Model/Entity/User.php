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
    protected $_virtual = ['full_name'];
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

    /**
     * Mark the account as verified when a valid token is provided within
     * expiration date.
     *
     * @param string $token random token string.
     * @param string $expiration the timestring duration of the token
     * @return bool return true if token is successfully verified
     */
    public function verify($token, $expiration = '+1 day')
    {
        $time = new Time($this->token_creation);
        if ($this->token == $token && $time->wasWithinLast($expiration)) {
            $this->verified = true;
        }
        return $this->verified;
    }
}
