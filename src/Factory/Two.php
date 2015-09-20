<?php

namespace Recca0120\Socialite\Factory;

use Illuminate\Http\Request;
use Recca0120\Socialite\Contracts\Provider as ProviderContract;
use Recca0120\Socialite\Factory\Traits\Stateless;
use Recca0120\Socialite\Factory\User\Two as User;
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
        $code = $this->getCode();
        $accessToken = $this->getAccessToken($code);
        $user = $this->mapUserToObject($this->getUserByToken($accessToken));

        return $user->setToken($accessToken);
    }

    /**
     * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    public function getAccessToken($code = '')
    {
        $service = $this->getService();
        if (empty($code) === true) {
            $token = $this->storage->retrieveAccessToken($service->service());
            // if ($token->isExpired() === true) {
            //     $this->getService()->refreshAccessToken($token);
            // }
        } else {
            $state = $this->request->input('state');
            $token = $service->requestAccessToken($code, $state);
        }

        return $token->getAccessToken();
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
