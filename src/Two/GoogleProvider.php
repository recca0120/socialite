<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Consumer\Credentials;
use OAuth\OAuth2\Service\Google;
use OAuth\ServiceFactory;
use Recca0120\Socialite\Factory\Two as ProviderFactory;

class GoogleProvider extends ProviderFactory
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        Google::SCOPE_GPLUS_ME,
        Google::SCOPE_GPLUS_LOGIN,
        Google::SCOPE_EMAIL,
    ];

    public $version = '';

    protected function createService(ServiceFactory $serviceFactory, Credentials $credentials)
    {
        return $serviceFactory->createService(
            $this->driver,
            $credentials,
            $this->storage,
            $this->scopes,
            null,
            $this->isStateless(),
            array_get($this->config, 'version', $this->version)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token = '')
    {
        $service = $this->getService();
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo';

        $response = $service->request($url, 'GET', null, [
            'Accept' => 'application/json',
            // 'Authorization' => 'Bearer '.$token,
        ]);

        return json_decode($response, true);
    }
}
