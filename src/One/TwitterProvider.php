<?php

namespace Recca0120\Socialite\One;

use OAuth\Common\Token\TokenInterface;

class TwitterProvider extends AbstractService
{
    protected function mapUserToObject(array $user)
    {
        $map = [
            'id' => array_get($user, 'id'),
            'nickname' => array_get($user, 'screen_name'),
            'name' => array_get($user, 'name'),
            'email' => array_get($user, 'email'),
            'avatar' => array_get($user, 'profile_image_url'),
        ];

        return with(new User)->setRaw($user)->map($map);
    }

    protected function getUserByToken(TokenInterface $token)
    {
        $url = '/account/verify_credentials.json?include_email=true';
        $response = $this->request($url, 'GET', null, [
        ]);

        return json_decode($response, true);
    }
}
