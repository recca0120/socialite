<?php

namespace Recca0120\Socialite\Factory;

use Illuminate\Http\Request;
use OAuth\Common\Service\AbstractService;
use Recca0120\Socialite\Contracts\Provider as ProviderContract;
use Recca0120\Socialite\Factory\Traits\Stateless;
use Recca0120\Socialite\Two\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class Two extends Provider implements ProviderContract
{
    use Stateless;

    /**
     * Redirect the user of the application to the provider's authentication screen.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        $service = $this->getService();
        if (isset($this->parameters['access_type']) && method_exists($service, 'setAccessType') == true) {
            $service->setAccessType($this->parameters['access_type']);
            unset($this->parameters['access_type']);
        }

        $url = $service->getAuthorizationUri($this->parameters)->getAbsoluteUri();

        return new RedirectResponse($url);
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        $token = $this->getToken();
        $user = $this->mapUserToObject($this->getUserByToken($token));

        return $user->setToken($token->getAccessToken());
    }

    /**
     * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    public function getAccessToken($code = '')
    {
        $token = $this->getToken($code);

        return $token->getAccessToken();
    }

    public function getToken($code = '')
    {
        $service = $this->getService();
        $parameters = array_merge([
            'code' => $code,
        ], $this->request->all());
        $token = $this->verifyAccessToken($service, $parameters);

        return $token;
    }

    public function verifyAccessToken(AbstractService $service, array $parameters)
    {
        $code = array_get($parameters, 'code');
        if (empty($code) === false || $this->storage->hasAccessToken($service->service()) === false) {
            $state = array_get($parameters, 'state');
            $token = $service->requestAccessToken($code, $state);
        } else {
            $token = $this->storage->retrieveAccessToken($service->service());
            if ($token->isExpired() === true) {
                $this->getService()->refreshAccessToken($token);
            }
        }

        return $token;
    }

    /**
     * Get the code from the request.
     *
     * @return string
     */
    protected function getCode()
    {
        return $this->request->input('code');
    }

    protected function getUserObject()
    {
        return new User;
    }
}
