<?php

namespace Recca0120\Socialite\Two;

use Illuminate\Http\Request;
use Recca0120\Socialite\AbstractProviderFactory;
use Recca0120\Socialite\Contracts\Provider as ProviderContract;
use Recca0120\Socialite\OAuthTraits\Stateless;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProviderFactory extends AbstractProviderFactory implements ProviderContract
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

        $url = $this->getService()->getAuthorizationUri($this->parameters)->getAbsoluteUri();

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
        if (empty($code) === true) {
            $token = $this->storage->retrieveAccessToken(ucfirst($this->driver));
            // if ($token->isExpired() === true) {
            //     $this->getService()->refreshAccessToken($token);
            // }
        } else {
            $state = $this->request->input('state');
            $service = $this->getService();
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

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user, $extra = [])
    {
        $mapUserToObject = $extra;
        foreach ($this->mapUserToObject as $key => $value) {
            $mapUserToObject[$key] = array_get($user, $value);
        }

        return (new User)->setRaw($user)->map($mapUserToObject);
    }
}
