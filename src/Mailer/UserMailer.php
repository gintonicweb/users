<?php

namespace Users\Mailer;

use Cake\Mailer\Mailer;

class UserMailer extends Mailer
{
    /**
     * Email asking user to register their email address upon
     * signup
     *
     * @param User $user The entity object of the targeted user
     * @return void
     */
    public function signup($user)
    {
        $this->_email
            ->profile('default')
            ->template('signup')
            ->emailFormat('html')
            ->to($user->email)
            ->subject(sprintf('Welcome %s', $user->name));

        $this->set([
            'userId' => $user->id,
            'first' => $user->first,
            'token' => $user->token
        ]);
    }

    /**
     * If a user haven't registered his email address, and lost the confirmation
     * email, he can request a new one.
     *
     * @param User $user The entity object of the targeted user
     * @return void
     */
    public function verification($user)
    {
        $this->_email
            ->profile('default')
            ->template('verification')
            ->emailFormat('html')
            ->to($user->email)
            ->subject('Account verification');

        $this->set([
            'userId' => $user->id,
            'first' => $user->first,
            'token' => $this->token,
        ]);
    }

    /**
     * Sent when a user ask for password recovery 
     *
     * @param User $user The entity object of the targeted user
     * @return void
     */
    public function recovery($user)
    {
        $this->_email
            ->profile('default')
            ->template('recovery')
            ->emailFormat('html')
            ->to($user->email)
            ->subject('Password recovery');

        $this->set([
            'userId' => $user->id,
            'first' => $user->first,
            'token' => $user->token
        ]);
    }
}
