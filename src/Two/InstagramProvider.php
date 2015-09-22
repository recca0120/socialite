<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Instagram;

class InstagramProvider extends AbstractService
{
    protected $scopes = [
        Instagram::SCOPE_BASIC,
    ];

    protected function mapUserToObject(array $user)
    {
        $map = [
            'id' => array_get($user, 'data.id'),
            'nickname' => array_get($user, 'data.username'),
            'name' => array_get($user, 'data.full_name'),
            'email' => null,
            'avatar' => array_get($user, 'data.profile_picture'),
        ];

        return with(new User)->setRaw($user)->map($map);
    }

    protected function getUserByToken(TokenInterface $token)
    {
        $url = 'users/self';
        $response = $this->request($url, 'GET', null, [
        ]);

        return json_decode($response, true);
    }
}
