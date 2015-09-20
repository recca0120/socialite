<?php

namespace Recca0120\Socialite\Two;

use Illuminate\Http\Request;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\SymfonySession as Storage;
use OAuth\ServiceFactory;
use Recca0120\Socialite\Factory\Two as ProviderFactory;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage as SessionStorage;

class GoogleServiceProvider extends ProviderFactory
{
    /**
     * Create a new provider instance.
     *
     * @param  string  $driver
     * @param  Request  $request
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  string  $redirectUrl
     * @return void
     */
    protected function createStorage()
    {
        $sessionId = md5(json_encode($this->scopes).json_encode($this->config));
        $session = new Session(new SessionStorage);
        $session->setId($sessionId);
        $session->start();

        $this->storage = new Storage($session);
        $session = $this->storage->getSession();

        return $this->storage;
    }

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
