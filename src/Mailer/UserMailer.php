<?php

namespace Users\Mailer;

use Cake\Mailer\Mailer;

class UserMailer extends Mailer
{
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
            ->emailFormat('text');
    }

    public function forgotPassword()
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
