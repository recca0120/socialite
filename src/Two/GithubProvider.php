<?php

namespace Recca0120\Socialite\Two;

use OAuth\OAuth2\Service\GitHub;
use Recca0120\Socialite\Factory\Two as ProviderFactory;

class GithubProvider extends ProviderFactory
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        // GitHub::SCOPE_USER,
        GitHub::SCOPE_USER_EMAIL,
    ];

    protected $mapUserToObject = [
        'id' => 'id',
        'nickname' => 'login',
        'name' => 'name',
        'email' => 'email',
        'avatar' => 'avatar_url',
    ];

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token = '')
    {
        $service = $this->getService();
        $url = 'user';

        $response = $service->request($url, 'GET', null, [
            'Accept' => 'application/json',
            // 'Authorization' => 'Bearer '.$token,
        ]);

        return json_decode($response, true);
    }
}
