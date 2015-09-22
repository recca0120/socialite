<?php

namespace Recca0120\Socialite\One;

use Recca0120\Socialite\Common\Service;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractService extends Service
{
    public function redirect()
    {
        $service = $this->getService();
        $token = $service->requestRequestToken();
        $url = $service->getAuthorizationUri(array_merge([
            'oauth_token' => $token->getRequestToken(),
        ], $this->parameters))->getAbsoluteUri();

        return new RedirectResponse($url);
    }

    public function user()
    {
        $token = $this->verifyToken();
        $user = $this->mapUserToObject($this->getUserByToken($token));
        $accessToken = $token->getAccessToken();
        $accessTokenSecret = $token->getAccessTokenSecret();

        return $user->setToken($accessToken, $accessTokenSecret);
    }

    protected function verifyToken()
    {
        $service = $this->getService();
        $token = $service->getStorage()->retrieveAccessToken($service->service());
        $oauthToken = $this->request->input('oauth_token');
        $oauthVerifier = $this->request->input('oauth_verifier');
        $token = $service->requestAccessToken($oauthToken, $oauthVerifier, $token->getRequestTokenSecret());

        return $token;
    }
}
