<?php

namespace Recca0120\Socialite\Two;

use OAuth\OAuth2\Service\Instagram;
use Recca0120\Socialite\Factory\Two as ProviderFactory;

class InstagramProvider extends ProviderFactory
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        Instagram::SCOPE_BASIC,
    ];

    protected $mapUserToObject = [
        'id' => 'data.id',
        'nickname' => 'login',
        'name' => 'data.name',
        'avatar' => 'data.profile_picture',
    ];

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token = '')
    {
        $service = $this->getService();
        $url = 'users/self';

        $response = $service->request($url, 'GET', null, [
            'Accept' => 'application/json',
            // 'Authorization' => 'Bearer '.$token,
        ]);

        return json_decode($response, true);
    }
}
