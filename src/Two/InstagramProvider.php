<?php

namespace Recca0120\Socialite\Two;

use OAuth\OAuth2\Service\Instagram;

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

    public function getProfileUrl()
    {
        return 'users/self';
    }
}
