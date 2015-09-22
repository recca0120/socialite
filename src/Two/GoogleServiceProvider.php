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
        return [__CLASS__, __DIR__, __FILE__];
    }

    public function getAccessToken()
    {
        if ($this->hasAccessToken() === false) {
            $token = $this->verifyToken();
        } else {
            $token = $this->retrieveAccessToken();
        }

        return $token->getAccessToken();
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
