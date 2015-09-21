<?php

namespace Recca0120\Socialite\Factory;

use Illuminate\Http\Request;
use OAuth\Common\Service\AbstractService;
use OAuth\Common\Token\TokenInterface;
use Recca0120\Socialite\Contracts\Provider as ProviderContract;
use Recca0120\Socialite\Factory\Traits\Stateless;
use Recca0120\Socialite\Two\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class Two extends Provider implements ProviderContract
{
    use Stateless;

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

    protected function getCode()
    {
        return $this->request->input('code');
    }

    public function user()
    {
        $token = $this->getToken();
        $user = $this->mapUserToObject($this->getUserByToken($token));

        return $user->setToken($token->getAccessToken());
    }

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

    protected function getUserObject()
    {
        return new User;
    }

    protected function getAuthorizationHeader(TokenInterface $token, array $extraHeader = [])
    {
        return $extraHeader;
    }
}
