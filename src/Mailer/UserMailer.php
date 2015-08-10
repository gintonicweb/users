<?php

namespace App\Mailer;

use Cake\Mailer\Mailer;

class UserMailer extends Mailer
{
    public function signup($user)
    {
        $this->_email
            ->profile('default');
            ->template('signup');
            ->emailFormat('html')
            ->to($user->email);
            ->subject(sprintf('Welcome %s', $user->name));

        $this->set([
            'userId' => $user->id,
            'first' => $user->first,
            'token' => $user->token
        ]);
    }

    public function verification($user)
    {
        $this->_email
            ->profile('default');
            ->template('verification');
            ->emailFormat('html')
            ->to($user->email);
            ->subject('Account verification');

        $this->set([
            'userId' => $user->id,
            'first' => $user->first,
            'token' => $this->token,
        ]);
    }

    public function recovery($user)
    {
        $this->_email
            ->profile('default');
            ->template('recovery');
            ->emailFormat('html')
            ->to($user->email);
            ->subject('Password recovery');

        $this->set([
            'userId' => $user->id,
            'first' => $user->first,
            'token' => $user->token
        ]);
    }
}
