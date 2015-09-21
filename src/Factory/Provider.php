<?php

namespace Recca0120\Socialite\Factory;

use Illuminate\Http\Request;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Service\AbstractService;
use OAuth\Common\Token\TokenInterface;
use Recca0120\Socialite\Contracts\Provider as ProviderContract;
use Recca0120\Socialite\Factory\Traits\Service;

abstract class Provider implements ProviderContract
{
    use Service;

    protected $request;

    protected $driver;

    protected $config;

    protected $parameters = [];

    protected $scopes = [];

    protected $credentials = null;

    public function __construct($driver, $config, Request $request = null)
    {
        $this->driver = $driver;
        $this->config = $config;
        if ($request === null) {
            $request = new Request;
        }
        $this->request = $request;
        $this->credentials = new Credentials(
            array_get($this->config, 'client_id'),
            array_get($this->config, 'client_secret'),
            array_get($this->config, 'redirect')
        );
    }

    public function scopes(array $scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function with(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function stateless()
    {
        return $this;
    }

    public static function factory($driver, $config, Request $request = null)
    {
        $classOne = '\\Recca0120\\Socialite\\One\\'.ucfirst($driver).'Provider';
        $classTwo = '\\Recca0120\\Socialite\\Two\\'.ucfirst($driver).'Provider';

        if (class_exists($classTwo) === true) {
            return new $classTwo($driver, $config, $request);
        } elseif (class_exists($classOne) === true) {
            return new $classOne($driver, $config, $request);
        } else {
            return new static($driver, $config, $request);
        }
    }

    abstract protected function mapUserToObject(array $user);

    abstract public function getAccessToken();

    abstract public function verifyAccessToken(AbstractService $service, array $parameters);

    abstract public function getToken();

    abstract protected function getUserByToken(TokenInterface $token);
}
