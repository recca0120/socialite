<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Google;

class GoogleProvider extends AbstractService
{
    protected $scopes = [
        Google::SCOPE_GPLUS_ME,
        Google::SCOPE_GPLUS_LOGIN,
        Google::SCOPE_EMAIL,
    ];

    protected function mapUserToObject(array $user)
    {
        $map = [
            'id' => array_get($user, 'id'),
            'nickname' => array_get($user, 'nickname'),
            'name' => array_get($user, 'name'),
            'email' => array_get($user, 'email'),
            'avatar' => array_get($user, 'picture'),
        ];

        return with(new User)->setRaw($user)->map($map);
    }

    protected function getUserByToken(TokenInterface $token)
    {
        $service = $this->getService();
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo';
        $response = $service->request($url, 'GET', null, [
        ]);

        return json_decode($response, true);
    }
}
