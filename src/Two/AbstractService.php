<?php

namespace Recca0120\Socialite\Two;

use Recca0120\Socialite\Common\Service;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractService extends Service
{
    protected $scopes = [];

    protected $parameters = [];

    protected $stateless = false;

    protected $version = null;

    public function isStateless()
    {
        return !$this->stateless;
    }

    public function stateless()
    {
        $this->stateless = true;

        return $this;
    }

    public function scopes($scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function with(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function redirect()
    {
        $service = $this->getService();
        if (isset($this->parameters['access_type']) && method_exists($service, 'setAccessType') == true) {
            $service->setAccessType($this->parameters['access_type']);
            unset($this->parameters['access_type']);
        }
        $url = $service->getAuthorizationUri(array_merge([
        ], $this->parameters))->getAbsoluteUri();

        return new RedirectResponse($url);
    }

    public function user()
    {
        $token = $this->verifyToken();
        $user = $this->mapUserToObject($this->getUserByToken($token));

        return $user->setToken($token->getAccessToken());
    }

    protected function verifyToken($parameters = [])
    {
        $service = $this->getService();
        $code = '';
        $state = null;
        if (isset($parameters['code']) === true) {
            $code = $parameters['code'];
        } else {
            $state = $this->request->input('state');
            $code = $this->request->input('code');
        }

        $token = $service->requestAccessToken($code, $state);

        return $token;
    }

    public function getAccessToken($code)
    {
        $token = $this->verifyToken([
            'code' => $code,
        ]);

        return $token->getAccessToken();
    }

    // public function getAccessToken()
    // {
    //     return $service = $this->getService()->requestAccessToken(null, null);
    // }
}
