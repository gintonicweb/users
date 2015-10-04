<?php

namespace App\Auth;

use Cake\Auth\AbstractPasswordHasher;

class BypassPasswordHasher extends AbstractPasswordHasher
{

    public function hash($password)
    {
        return $password;
    }

    public function check($password, $hashedPassword)
    {
        return $password === $hashedPassword;
    }
}

