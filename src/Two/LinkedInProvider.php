<?php

namespace Recca0120\Socialite\Two;

use OAuth\OAuth2\Service\Linkedin;
use Recca0120\Socialite\Factory\Two as ProviderFactory;

class LinkedInProvider extends ProviderFactory
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        Linkedin::SCOPE_R_BASICPROFILE,
        Linkedin::SCOPE_R_EMAILADDRESS,
    ];

    /**
     * The fields that are included in the profile.
     *
     * @var array
     */
    protected $fields = [
        'id',
        'first-name',
        'last-name',
        'formatted-name',
        'email-address',
        'headline',
        'location',
        'industry',
        'public-profile-url',
        'picture-url',
        'picture-urls::(original)',
    ];

    protected $mapUserToObject = [
        'id' => 'id',
        'name' => 'formattedName',
        'email' => 'emailAddress',
        'avatar' => 'pictureUrl',
    ];

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user, $extra = [])
    {
        $extra = [
            'avatar_original' => array_get($user, 'pictureUrls.values.0'),
        ];

        return parent::mapUserToObject($user, $extra);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token = '')
    {
        $service = $this->getService();
        $url = 'users/self';

        $response = $service->request($url, 'GET', null, [
            'Accept' => 'application/json',
            'x-li-format' => 'json',
            // 'Authorization' => 'Bearer '.$token,
        ]);

        return json_decode($response, true);
    }
}
