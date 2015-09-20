<?php

namespace Recca0120\Socialite\One;

class TwitterProvider extends ProviderFactory
{
    protected $mapUserToObject = [
        'id' => 'id',
        'nickname' => 'screen_name',
        'name' => 'name',
        'email' => 'email',
        'avatar' => 'profile_image_url',
    ];

    public function getProfileUrl()
    {
        return '/account/verify_credentials.json?include_email=true';
    }
}
