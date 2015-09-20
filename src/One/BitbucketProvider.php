<?php

namespace Recca0120\Socialite\One;

use Illuminate\Http\Request;
use Recca0120\Socialite\Factory\One as ProviderFactory;

class BitbucketProvider extends ProviderFactory
{
    protected $mapUserToObject = [
        'id' => 'user.username',
        'nickname' => 'user.username',
        'name' => 'user.display_name',
        'avatar' => 'user.avatar',
    ];

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user, $extra = [])
    {
        $service = $this->getService();

        $user['emails'] = json_decode($service->request('/users/'.array_get($user, 'user.username').'/emails'), true);
        $extra = [
            'email' => array_get($user, 'emails.0.email'),
        ];

        return parent::mapUserToObject($user, $extra);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token = '')
    {
        $service = $this->getService();
        $url = '/user';

        $response = $service->request($url, 'GET', null, [
            'Accept' => 'application/json',
            // 'Authorization' => 'Bearer '.$token,
        ]);

        return json_decode($response, true);
    }
}
