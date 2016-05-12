<?php

namespace Users\Mailer;

use Cake\Mailer\Mailer;

class UserMailer extends Mailer
{
    /**
     * Email sent on registration
     *
     * @param array $user User information, must includer email and username
     * @param string $token Token used for validation
     * @return \Cake\Mailer\Mailer
     */
    public function register($user, $token)
    {
        return $this->to($user['email'])
            ->subject('Please confirm your email')
            ->template('Users.welcome')
            ->layout(false)
            ->set([
                'username' => $user['username'],
                'token' => $token,
            ])
            ->emailFormat('html');
    }

    /**
     * Email sent on password recovery requests
     *
     * @param array $user User information, must includer email and username
     * @param string $token Token used for validation
     * @return \Cake\Mailer\Mailer
     */
    public function forgotPassword($user, $token)
    {
        return $this->to($user['email'])
            ->subject('Set your password')
            ->template('Users.forgotPassword')
            ->layout(false)
            ->set([
                'username' => $user['username'],
                'token' => $token,
            ])
            ->emailFormat('html');
    }
}
