<?php

namespace Recca0120\Socialite\Factory\Traits;

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
                    CURLOPT_CAINFO => __DIR__.'/../../../cert/ca-bundle.crt',
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

    public function __call($method, $parameters)
    {
        $service = $this->getService();
        if (method_exists($service, $method)) {
            return call_user_func_array([$this->getService(), $method], $parameters);
        } else {
            $trace = array_reverse(debug_backtrace());
            $message = 'Call to undefined method '.$trace[0]['class'].'::'.$method.'()';
            $code = 0;
            $severity = 1;
            $file = $trace[0]['file'];
            $line = $trace[0]['line'] - 2;
            throw new \ErrorException($message, $code, $severity, $file, $line);
        }
    }
}
