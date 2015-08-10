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
        'verified' => false,
        'token' => false,
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
     * Virtual filed for full name of user.
     * return the concated string of first and last name of user as Full Name.
     *
     * @return boolean | string full name of user.
     */
    protected function _getFullName()
    {
        if (isset($this->_properties['first']) && isset($this->_properties['last'])) {
            return $this->_properties['first'] . ' ' . $this->_properties['last'];
        }
        return false;
    }

    /**
     * The token is designed to expire after some amount of time. This
     * method refreshes the token.
     *
     * @return void
     */
    public function updateToken()
    {
        $this->token = md5(uniqid(rand(), true));
        $this->token_creation = Time::now();
    }

    /**
     * Mark the account as verified when a valid token is provided within
     * expiration date.
     *
     * @param string $token random token string.
     * @param string $expiration the timestring duration of the token
     * @return boolean return true if token is successfully verified
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
