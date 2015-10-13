<?php

namespace Recca0120\Socialite\Common;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Session\FileSessionHandler;
use Illuminate\Session\Store as LaravelSession;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Service\AbstractService;
use OAuth\Common\Storage\SymfonySession as Storage;
// use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage as SymfonyFileHandler;
use OAuth\ServiceFactory;
use Recca0120\Socialite\Contracts\Provider as ProviderContract;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage as SymfonyNativeSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage as SymfonyPhpBridgeSessionHandler;

abstract class Service implements ProviderContract
{
    protected $service = null;

    protected static $serviceFactory;

    protected static $storage;

    protected static $session = null;

    protected $driver;

    protected $config;

    protected $request;

    protected $credentials;

    protected $scopes = [];

    protected $parameters = [];

    protected $apiVersion = null;

    public function __construct($driver, $config, Request $request = null)
    {
        $this->driver = $driver;

        if ($request === null) {
            $request = new Request;
        }
        $this->request = $request;

        $this->config = $config;

        $this->credentials = new Credentials(
            array_get($this->config, 'client_id'),
            array_get($this->config, 'client_secret'),
            array_get($this->config, 'redirect')
        );
    }

    protected function registerService()
    {
        return;
    }

    protected function getServiceFactory()
    {
        if ((static::$serviceFactory instanceof ServiceFactory) === false) {
            static::$serviceFactory = new ServiceFactory;
            $httpClient = null;
            if (function_exists('curl_version') === true) {
                $httpClient = new CurlClient;
                $httpClient->setCurlParameters([
                    CURLOPT_CAINFO => __DIR__.'/../../cert/cacert.pem',
                ]);
            }
            static::$serviceFactory->setHttpClient($httpClient);
        }

        return static::$serviceFactory;
    }

    protected function createService($serviceFactory, $sessionId)
    {
        return $serviceFactory->createService(
            $this->driver,
            $this->credentials,
            $this->createStorage($sessionId),
            $this->scopes,
            null,
            array_get($this->config, 'version', $this->apiVersion)
        );
    }

    protected function getService()
    {
        if ($this->service instanceof AbstractService === false) {
            $sessionId = $this->getSessionId();
            $serviceFactory = $this->getServiceFactory();
            $registerService = $this->registerService();
            if ($registerService !== null) {
                $serviceFactory->registerService($this->driver, $registerService);
            }
            $this->service = $this->createService($serviceFactory, $sessionId);
        }

        return $this->service;
    }

    protected function createStorage($sessionId = null)
    {
        if ($sessionId !== null) {
            // $name = '/Google_Client';
            // $path = session_save_path().'/'.$name;
            $name = 'Recca0120Socialite';
            $path = sys_get_temp_dir().'/'.$name;

            $sessionId = sha1(serialize($sessionId));
            $file = new Filesystem;
            if ($file->isDirectory($path) === false) {
                $file->makeDirectory($path, 0755, true);
            }

            $handler = new FileSessionHandler($file, $path);
            $session = new LaravelSession($name, $handler, $sessionId);
            // $handler = new SymfonyFileHandler($path);
            // $session = new SymfonySession($handler);
            $session->setId($sessionId);
            $session->start();
            $this->saveSessionWhenShutdown($session);
        } else {
            $session = static::$session;
            if ($session === null) {
                $session = $this->request->getSession();
                if ($session === null) {
                    if (session_status() == PHP_SESSION_NONE) {
                        $handler = new SymfonyNativeSessionHandler;
                    } else {
                        $handler = new SymfonyPhpBridgeSessionHandler;
                    }
                    $session = new SymfonySession($handler);
                    $session->start();
                    $this->saveSessionWhenShutdown($session);
                }
                static::$session = $session;
            }
        }
        $storage = new Storage($session);

        return $storage;
    }

    protected function saveSessionWhenShutdown($session)
    {
        register_shutdown_function(function () use ($session) {
            if ($session->isStarted() === true) {
                $session->save();
            }
        });
    }

    public static function factory($driver, $config, Request $request = null)
    {
        $provider = ucfirst($driver).'Provider';
        $classFormat = '\\Recca0120\\Socialite\\%s\\'.ucfirst($driver).'Provider';
        foreach (['One', 'Two'] as $namespace) {
            $class = sprintf($classFormat, $namespace);
            if (class_exists($class) === true) {
                return new $class($driver, $config, $request);
            }
        }
    }

    protected function getSessionId()
    {
        return;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function getToken()
    {
        return $token = $service->getStorage()->retrieveAccessToken($this->getServiceName());
    }

    public function request($path, $method = 'GET', $body = null, array $extraHeaders = [])
    {
        $service = $this->getService();

        return $service->request($path, $method, $body, $extraHeaders);
    }

    public function getServiceName()
    {
        return $service = $this->getService()->service();
    }

    public function getStorage()
    {
        return $this->getService()->getStorage();
    }

    public function hasAccessToken()
    {
        return $this->getStorage()->hasAccessToken($this->getServiceName());
    }

    public function retrieveAccessToken()
    {
        $token = $this->getStorage()->retrieveAccessToken($this->getServiceName());

        return $token;
    }

    abstract public function redirect();

    abstract protected function mapUserToObject(array $user);

    abstract protected function verifyToken();
}
