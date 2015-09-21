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

    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The PHPoAuth driver.
     *
     * @var string
     */
    protected $driver;

    /**
     * The config.
     *
     * @var array
     */
    protected $config;

    /**
     * The custom parameters to be sent with the request.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [];

    protected $credentials = null;

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

    /**
     * Set the scopes of the requested access.
     *
     * @param  array  $scopes
     * @return $this
     */
    public function scopes(array $scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * Set the request instance.
     *
     * @param  Request  $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set the custom parameters of the request.
     *
     * @param  array  $parameters
     * @return $this
     */
    public function with(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function stateless()
    {
        return $this;
    }

    public function getProfileUrl()
    {
        return '';
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

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array  $user
     * @return \Recca0120\Socialite\User
     */
    abstract protected function mapUserToObject(array $user);

    /**
     * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    abstract public function getAccessToken();

    /**
     * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    abstract public function verifyAccessToken(AbstractService $service, array $parameters);

    /**
     * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    abstract public function getToken();

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    abstract protected function getUserByToken(TokenInterface $token);
}
