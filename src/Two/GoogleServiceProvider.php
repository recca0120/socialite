<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Token\TokenInterface;
use OAuth\ServiceFactory;
use Recca0120\Socialite\Factory\Two as ProviderFactory;

class GoogleServiceProvider extends ProviderFactory
{
    protected function createService(ServiceFactory $serviceFactory, $sessionId = null)
    {
        $serviceFactory->registerService('googleservice', '\Recca0120\Socialite\OAuthLib\OAuth2\GoogleService');
        $sessionId = [
            $this->credentials,
            $this->scopes,
        ];

        return parent::createService($serviceFactory, $sessionId);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken(TokenInterface $token)
    {
        return [];
    }
}
