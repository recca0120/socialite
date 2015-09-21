<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\GitHub;
use Recca0120\Socialite\Factory\Two as ProviderFactory;

class GithubProvider extends ProviderFactory
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

        return $this->getUserObject()->setRaw($user)->map($map);
    }

    protected function getUserByToken(TokenInterface $token)
    {
        $service = $this->getService();
        $url = 'user';

        $response = $service->request($url, 'GET', null, $this->getAuthorizationHeader($token, [
            // 'Accept' => 'application/json',
        ]));

        return json_decode($response, true);
    }
}
