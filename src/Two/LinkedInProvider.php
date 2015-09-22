<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Linkedin;

class LinkedInProvider extends AbstractService
{
    protected $scopes = [
        Linkedin::SCOPE_R_BASICPROFILE,
        Linkedin::SCOPE_R_EMAILADDRESS,
    ];

    protected function mapUserToObject(array $user)
    {
        $map = [
            'id' => array_get($user, 'id'),
            'nickname' => null,
            'name' => array_get($user, 'formattedName'),
            'email' => array_get($user, 'emailAddress'),
            'avatar' => array_get($user, 'pictureUrl'),
            'avatar_original' => array_get($user, 'pictureUrls.values.0'),
        ];

        return with(new User)->setRaw($user)->map($map);
    }

    protected function getUserByToken(TokenInterface $token)
    {
        $service = $this->getService();
        $fields = [
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
        $url = 'https://api.linkedin.com/v1/people/~:('.implode(',', $fields).')';

        $response = $service->request($url, 'GET', null, [
            'x-li-format' => 'json',
        ]);

        return json_decode($response, true);
    }
}
