<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Token\TokenInterface;
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

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        $map = [
            'id' => array_get($user, 'data.id'),
            'nickname' => array_get($user, 'data.username'),
            'name' => array_get($user, 'data.full_name'),
            'email' => null,
            'avatar' => array_get($user, 'data.profile_picture'),
        ];

        return $this->getUserObject()->setRaw($user)->map($map);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken(TokenInterface $token)
    {
        $service = $this->getService();
        $url = 'users/self';

        $response = $service->request($url, 'GET', null, [
            // 'Accept' => 'application/json',
            // 'Authorization' => 'Bearer '.$token,
        ]);

        return json_decode($response, true);
    }
}
