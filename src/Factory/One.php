<?php

namespace Recca0120\Socialite\Factory;

use Illuminate\Http\Request;
use Recca0120\Socialite\Contracts\Provider as ProviderContract;
use Recca0120\Socialite\Factory\User\One as User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class One extends Provider implements ProviderContract
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return RedirectResponse
     */
    public function redirect()
    {
        $service = $this->getService();
        $token = $service->requestRequestToken();

        $url = $this->getService()->getAuthorizationUri(array_merge($this->parameters, [
            'oauth_token' => $token->getRequestToken(),
        ]))->getAbsoluteUri();

        return new RedirectResponse($url);
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        $token = $this->getToken();
        $accessToken = $token->getAccessToken();
        $accessTokenSecret = $token->getAccessTokenSecret();
        $user = $this->mapUserToObject($this->getUserByToken());

        return $user->setToken($accessToken, $accessTokenSecret);
    }

    /**
     * Get the access token for the given code.
     *
     * @return string
     */
    public function getToken()
    {
        $service = $this->getService();
        $token = $this->storage->retrieveAccessToken($service->service());
        $oauthToken = $this->request->get('oauth_token');
        $oauthVerifier = $this->request->get('oauth_verifier');
        if (empty($oauthToken) === false) {
            $token = $service->requestAccessToken($oauthToken, $oauthVerifier, $token->getRequestTokenSecret());
        }

        return $token;
    }

    protected function getUserObject()
    {
        return new User;
    }
}
