<?php

namespace Recca0120\Socialite\One;

use Recca0120\Socialite\Factory\One as ProviderFactory;

class TwitterProvider extends ProviderFactory
{
    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        $map = [
            'id' => array_get($user, 'id'),
            'nickname' => array_get($user, 'screen_name'),
            'name' => array_get($user, 'name'),
            'email' => array_get($user, 'email'),
            'avatar' => array_get($user, 'profile_image_url'),
        ];

        return $this->getUserObject()->setRaw($user)->map($map);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token = '', $secret = '')
    {
        $service = $this->getService();
        $url = '/account/verify_credentials.json?include_email=true';

        $response = $service->request($url, 'GET', null, [
            // 'Accept' => 'application/json',
            // 'Authorization' => 'Bearer '.$token,
        ]);

        return json_decode($response, true);
    }
}
