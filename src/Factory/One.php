<?php

namespace Recca0120\Socialite\Factory;

use Illuminate\Http\Request;
use Recca0120\Socialite\Contracts\Provider as ProviderContract;
use Recca0120\Socialite\One\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use OAuth\Common\Service\AbstractService;
abstract class One extends Provider implements ProviderContract
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
        $token = $this->getAccessToken();
        $accessToken = $token->getAccessToken();
        $accessTokenSecret = $token->getAccessTokenSecret();
        $user = $this->mapUserToObject($this->getUserByToken($accessToken, $accessTokenSecret));
        return $user->setToken($accessToken, $accessTokenSecret);
    }

    /**
     * Get the access token for the given code.
     *
     * @return string
     */
    public function getAccessToken($oauthToken = '', $oauthVerifier = '')
    {
        $service = $this->getService();
        $parameters = array_merge([
            'oauth_token' => $oauthToken,
            'oauth_verifier' => $oauthVerifier
        ], $this->request->all());
        $token = $this->verifyAccessToken($service, $parameters);
        return $token;
    }

    public function verifyAccessToken(AbstractService $service, array $parameters) {
        $token = $this->storage->retrieveAccessToken($service->service());
        $oauthToken = array_get($parameters, 'oauth_token');
        $oauthVerifier = array_get($parameters, 'oauth_verifier');
        if (empty($oauthToken) === false && empty($oauthVerifier) === false) {
            $token = $service->requestAccessToken($oauthToken, $oauthVerifier, $token->getRequestTokenSecret());
        }
        return $token;
    }

    protected function getUserObject()
    {
        return new User;
    }

    /**
     * Determine if the request has the necessary OAuth verifier.
     *
     * @return bool
     */
    protected function hasNecessaryVerifier()
    {
        return $this->request->has('oauth_token') && $this->request->has('oauth_verifier');
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    abstract protected function getUserByToken($token = '', $secret = '');
}
