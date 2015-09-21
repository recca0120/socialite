<?php

namespace Recca0120\Socialite\Factory;

use Illuminate\Http\Request;
use OAuth\Common\Service\AbstractService;
use OAuth\Common\Token\TokenInterface;
use Recca0120\Socialite\Contracts\Provider as ProviderContract;
use Recca0120\Socialite\One\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class One extends Provider implements ProviderContract
{
    public function redirect()
    {
        $service = $this->getService();
        $token = $service->requestRequestToken();

        $url = $this->getService()->getAuthorizationUri(array_merge($this->parameters, [
            'oauth_token' => $token->getRequestToken(),
        ]))->getAbsoluteUri();

        return new RedirectResponse($url);
    }

    public function user()
    {
        $token = $this->getToken();
        $user = $this->mapUserToObject($this->getUserByToken($token));
        $accessToken = $token->getAccessToken();
        $accessTokenSecret = $token->getAccessTokenSecret();

        return $user->setToken($accessToken, $accessTokenSecret);
    }

    public function getAccessToken($oauthToken = '', $oauthVerifier = '')
    {
        $this->getToken($oauthToken, $oauthVerifier);

        return '';
    }

    public function getToken($oauthToken = '', $oauthVerifier = '')
    {
        $service = $this->getService();
        $parameters = array_merge([
            'oauth_token' => $oauthToken,
            'oauth_verifier' => $oauthVerifier,
        ], $this->request->all());
        $token = $this->verifyAccessToken($service, $parameters);

        return $token;
    }

    public function verifyAccessToken(AbstractService $service, array $parameters)
    {
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

    protected function getAuthorizationHeader(TokenInterface $token, array $extraHeader = [])
    {
        return $extraHeader;
    }
}
