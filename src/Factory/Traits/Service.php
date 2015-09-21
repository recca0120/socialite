<?php

namespace Recca0120\Socialite\Factory\Traits;

// use Illuminate\Filesystem\Filesystem;
// use Illuminate\Session\FileSessionHandler;
// use Illuminate\Session\Store as LaravelSession;

use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Service\AbstractService;
use OAuth\Common\Storage\SymfonySession as Storage;
use OAuth\ServiceFactory;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage as SymfonyFileHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage as SymfonyNativeSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage as SymfonyPhpBridgeSessionHandler;

trait Service
{
    /**
     * The Storage.
     *
     * @var Storage
     */
    protected $storage;

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
            $this->service = $this->createService($serviceFactory);
        }

        return $this->service;
    }

    protected function createStorage($sessionId = null)
    {
        if ($sessionId !== null) {
            $name = 'Recca0120Socialite';
            $sessionId = sha1(serialize($sessionId));
            $path = sys_get_temp_dir().'/'.$name;
            // $name = 'Recca0120Socialite';
            // $file = new Filesystem;
            // $path = session_save_path();
            // $handler = new FileSessionHandler($file, $path);
            // $session = new LaravelSession($name, $handler, $sessionId);
            // $session->start();
            // $this->registerShutdown();
            $handler = new SymfonyFileHandler($path);
            $session = new SymfonySession($handler);
            $session->setId($sessionId);
            $session->start();
            $this->registerShutdown();
        } else {
            $session = $this->request->getSession();
            if ($session === null) {
                if (session_status() == PHP_SESSION_NONE) {
                    $handler = new SymfonyNativeSessionHandler;
                } else {
                    $handler = new SymfonyPhpBridgeSessionHandler;
                }
                $session = new SymfonySession($handler);
                $session->start();
                $this->registerShutdown();
            }
        }
        $this->storage = new Storage($session);

        return $this->storage;
    }

    public function registerShutdown()
    {
        register_shutdown_function(function () {
            $session = $this->storage->getSession();
            $session->save();
        });
    }

    protected function createService(ServiceFactory $serviceFactory, $sessionId = null)
    {
        $service = $serviceFactory->createService(
            $this->driver,
            $this->credentials,
            $this->createStorage($sessionId),
            $this->scopes
        );

        return $service;
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
