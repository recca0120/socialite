<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\GitHub;

class GithubProvider extends AbstractService
{
    protected $scopes = [
        // GitHub::SCOPE_USER,
        GitHub::SCOPE_USER_EMAIL,
    ];

    protected function mapUserToObject(array $user)
    {
        $map = [
            'id' => array_get($user, 'id'),
            'nickname' => array_get($user, 'login'),
            'name' => array_get($user, 'name'),
            'email' => array_get($user, 'email'),
            'avatar' => array_get($user, 'avatar_url'),
        ];

        return with(new User)->setRaw($user)->map($map);
    }

    protected function getUserByToken(TokenInterface $token)
    {
        $url = 'user';
        $response = $this->request($url, 'GET', null, [
        ]);

        return json_decode($response, true);
    }
}
