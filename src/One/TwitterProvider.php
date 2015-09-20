<?php

namespace Recca0120\Socialite\One;

use Recca0120\Socialite\Factory\One as ProviderFactory;

class TwitterProvider extends ProviderFactory
{
    protected $mapUserToObject = [
        'id' => 'id',
        'nickname' => 'screen_name',
        'name' => 'name',
        'email' => 'email',
        'avatar' => 'profile_image_url',
    ];

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token = '')
    {
        $service = $this->getService();
        $url = '/account/verify_credentials.json?include_email=true';

        $response = $service->request($url, 'GET', null, [
            'Accept' => 'application/json',
            // 'Authorization' => 'Bearer '.$token,
        ]);

        return json_decode($response, true);
    }
}
