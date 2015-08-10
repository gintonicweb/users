<?php

namespace App\Mailer;

use Cake\Mailer\Mailer;

class UserMailer extends Mailer
{
    /**
     * Email meant to invite user to register their email address upon 
     * registration
     *
     * @param User $user The entity object of the targeted user
     */
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

    /**
     * If a user haven't registered his email address, a notification will
     * inform him that he needs to do it. If he has lost the confirmation
     * email, he can request a new one with this.
     *
     * @param User $user The entity object of the targeted user
     */
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

    /**
     * This is the email message that is sent upon the password recovery procedure
     *
     * @param User $user The entity object of the targeted user
     */
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
