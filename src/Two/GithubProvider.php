<?php

namespace Recca0120\Socialite\Two;

use OAuth\OAuth2\Service\GitHub;

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

    public function getProfileUrl()
    {
        return 'user';
    }
}
