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
 * @since         0.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace GintonicCMS\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
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
        'password' => false,
        'token' => false,
        '*' => true
    ];
    protected $_virtual = ['full_name'];
    protected $_hidden = ['password'];

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
     * Send Recovery Email to given email id.
     * For Example:
     * $user = $this->Users->get($id);
     * $user->sendRecovery();
     *
     * @return boolean True if email is send else False.
     */
    public function sendRecovery()
    {
        $email = new Email('default');
        $email->viewVars([
            'userId' => $this->id,
            'token' => $this->token
        ]);
        $email->template('GintonicCMS.recovery')
            ->emailFormat('html')
            ->to($this->email)
            ->subject('Forgot Password');
        return $email->send();
    }

    /**
     * Send Signup Email to given email id.
     * For Example:
     * $user = $this->Users->get($id);
     * $user->sendSignup();
     *
     * @return boolean True if email is send
     */
    public function sendSignup()
    {
        $email = new Email('default');
        $email->viewVars([
            'userId' => $this->id,
            'token' => $this->token
        ]);
        $email->template('GintonicCMS.signup')
            ->emailFormat('html')
            ->to($this->email)
            ->subject('Account validation');
        return $email->send();
    }

    /**
     * Send Verification Email to given email id after successfull Signup of user.
     * For Example:
     * $user = $this->Users->get($id);
     * $user->sendVerification();
     *
     * @return boolean True if email is send else False.
     */
    public function sendVerification()
    {
        $email = new Email('default');
        $email->viewVars([
            'userId' => $this->id,
            'token' => $this->token,
            'userName' => $this->full_name
        ]);
        $email->template('GintonicCMS.verification')
            ->emailFormat('html')
            ->to($this->email)
            ->subject('Account validation');
        return $email->send();
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
