<?php

namespace Recca0120\Socialite\One;

use Illuminate\Http\Request;
use OAuth\Common\Token\TokenInterface;

class BitbucketProvider extends AbstractService
{
    protected function mapUserToObject(array $user)
    {
        $service = $this->getService();

        $user['emails'] = json_decode($service->request('/users/'.array_get($user, 'user.username').'/emails'), true);

        $map = [
            'id' => array_get($user, 'user.username'),
            'nickname' => array_get($user, 'user.username'),
            'name' => array_get($user, 'user.display_name'),
            'email' => array_get($user, 'emails.0.email'),
            'avatar' => array_get($user, 'user.avatar'),
        ];

        return with(new User)->setRaw($user)->map($map);
    }

    protected function getUserByToken(TokenInterface $token)
    {
        $service = $this->getService();
        $url = '/user';

        $response = $service->request($url, 'GET', null);

        return json_decode($response, true);
    }
}
