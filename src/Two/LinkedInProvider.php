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

    protected $extraHeaders = [
        'x-li-format' => 'json',
    ];

    public function getProfileUrl()
    {
        $fields = implode(',', $this->fields);
        $url = 'https://api.linkedin.com/v1/people/~:('.$fields.')';

        return $url;
    }

    protected $mapUserToObject = [
        'id' => 'id',
        'name' => 'formattedName',
        'email' => 'emailAddress',
        'avatar' => 'pictureUrl',
    ];

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        $extra = [
            'avatar_original' => array_get($user, 'pictureUrls.values.0'),
        ];

        return parent::mapUserToObject($user, $extra);
    }
}
