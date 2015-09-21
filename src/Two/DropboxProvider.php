<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Token\TokenInterface;
use Recca0120\Socialite\Factory\Two as ProviderFactory;

class DropboxProvider extends ProviderFactory
{
    protected $scopes = [];

    protected function mapUserToObject(array $user)
    {
        $map = [
            'id' => array_get($user, 'uid'),
            'nickname' => null,
            'name' => array_get($user, 'display_name'),
            'email' => array_get($user, 'email'),
            'avatar' => null,
        ];

        return $this->getUserObject()->setRaw($user)->map($map);
    }

    protected function getUserByToken(TokenInterface $token)
    {
        $service = $this->getService();

        $url = 'https://api.dropbox.com/1/account/info';

        $response = $service->request($url, 'GET', null, $this->getAuthorizationHeader($token, [
            // 'Accept' => 'application/json',
        ]));

        return json_decode($response, true);
    }
}
