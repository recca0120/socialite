<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Facebook;

class FacebookProvider extends AbstractService
{
    protected $scopes = [
        Facebook::SCOPE_PUBLIC_PROFILE,
        Facebook::SCOPE_EMAIL,
    ];

    public $apiVersion = 'v2.4';

    public $graphUrl = 'https://graph.facebook.com';

    public function registerService()
    {
        return '\Recca0120\Socialite\OAuthLib\OAuth2\Facebook';
    }

    protected function mapUserToObject(array $user)
    {
        $map = [
            'id' => array_get($user, 'id'),
            'nickname' => array_get($user, 'nickname'),
            'name' => array_get($user, 'name'),
            'email' => array_get($user, 'email'),
        ];
        $map['avatar'] = $this->graphUrl.'/'.$this->version.'/'.array_get($user, 'id', 'me').'/picture';
        $map['avatar_original'] = $map['avatar'].'?width=1920';
        if (empty($map['name']) == true) {
            $map['name'] = array_get($user, 'first_name').' '.array_get($user, 'last_name');
        }

        return with(new User)->setRaw($user)->map($map);
    }

    protected function getUserByToken(TokenInterface $token)
    {
        $service = $this->getService();
        $fields = ['first_name', 'last_name', 'name', 'email', 'gender', 'verified'];
        $url = '/me?fields='.implode(',', $fields);
        $response = $service->request($url, 'GET', null, [
        ]);

        return json_decode($response, true);
    }
}
