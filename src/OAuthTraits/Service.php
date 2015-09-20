<?php

namespace Recca0120\Socialite\OAuthTraits;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Service\AbstractService;
use OAuth\ServiceFactory;

trait Service
{
    /**
     * The scopes being requested.
     *
     * @var Service
     */
    protected $service = null;

    public function getService()
    {
        if ($this->service instanceof AbstractService === false) {
            $serviceFactory = new ServiceFactory;
            $httpClient = null;
            if (function_exists('curl_version') === true) {
                $httpClient = new CurlClient;
                $httpClient->setCurlParameters([
                    CURLOPT_CAINFO => __DIR__.'/../../cert/ca-bundle.crt',
                ]);
            }
            $serviceFactory->setHttpClient($httpClient);
            $credentials = new Credentials(
                array_get($this->config, 'client_id'),
                array_get($this->config, 'client_secret'),
                array_get($this->config, 'redirect')
            );
            $this->service = $this->createService($serviceFactory, $credentials);
        }

        return $this->service;
    }

    protected function createService(ServiceFactory $serviceFactory, Credentials $credentials)
    {
        return $serviceFactory->createService(
            $this->driver,
            $credentials,
            $this->storage,
            $this->scopes
        );
    }
}
