<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Token\TokenInterface;

class DropboxProvider extends AbstractService
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

        return with(new User)->setRaw($user)->map($map);
    }

    protected function getUserByToken(TokenInterface $token)
    {
        $url = 'https://api.dropbox.com/1/account/info';
        $response = $this->request($url, 'GET', null, [
        ]);

        return json_decode($response, true);
    }
}
