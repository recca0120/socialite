<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;
use Recca0120\Socialite\Factory\Two as ProviderFactory;

class GoogleServiceProvider extends ProviderFactory
{
    protected function createService(ServiceFactory $serviceFactory, Credentials $credentials)
    {
        $serviceFactory->registerService('googleservice', '\Recca0120\Socialite\OAuthLib\OAuth2\GoogleService');

        return parent::createService($serviceFactory, $credentials);
    }

    /**
     * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    public function getAccessToken($code = '')
    {
        $service = $this->getService();

        if ($this->storage->hasAccessToken($service->service()) === true) {
            $token = $this->storage->retrieveAccessToken($service->service());
            if ($token->isExpired() === true) {
                $token = $service->refreshAccessToken($token);
            }

            return $token->getAccessToken();
        }

        $state = $this->request->input('state');
        $token = $service->requestAccessToken($code, $state);

        return $token->getAccessToken();
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
    protected function getUserByToken($token = '')
    {
        return [];
    }
}
