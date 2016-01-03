<?php

namespace Recca0120\Socialite\Two;

use Recca0120\Socialite\AbstractUser;

class User extends AbstractUser
{
    /**
     * The user's access token.
     *
     * @var string
     */
    public $token;

    /**
     * Set the token on the user.
     *
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }
}
