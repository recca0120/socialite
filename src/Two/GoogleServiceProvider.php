<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Token\TokenInterface;

class GoogleServiceProvider extends AbstractService
{
    public function registerService()
    {
        return '\Recca0120\Socialite\OAuthLib\OAuth2\GoogleService';
    }

    protected function getSessionId()
    {
        return [__CLASS__, __FILE__];
    }

    protected function mapUserToObject(array $user)
    {
        return [];
    }

    protected function getUserByToken(TokenInterface $token)
    {
        return [];
    }
}
