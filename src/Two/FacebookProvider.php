<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Facebook;
use OAuth\ServiceFactory;
use Recca0120\Socialite\Factory\Two as ProviderFactory;

class FacebookProvider extends ProviderFactory
{
    protected $scopes = [
        Facebook::SCOPE_PUBLIC_PROFILE,
        Facebook::SCOPE_EMAIL,
    ];

    public $version = 'v2.4';

    public $graphUrl = 'https://graph.facebook.com';

    protected function createService(ServiceFactory $serviceFactory, $sessionId = null)
    {
        $serviceFactory->registerService('facebook', '\Recca0120\Socialite\OAuthLib\OAuth2\Facebook');

        return $serviceFactory->createService(
            $this->driver,
            $this->credentials,
            $this->createStorage($sessionId),
            $this->scopes,
            null,
            array_get($this->config, 'version', $this->version)
        );
    }

    /**
     * {@inheritdoc}
     */
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

        return $this->getUserObject()->setRaw($user)->map($map);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken(TokenInterface $token)
    {
        $service = $this->getService();
        $fields = ['first_name', 'last_name', 'name', 'email', 'gender', 'verified'];
        $url = '/me?fields='.implode(',', $fields);

        $response = $service->request($url, 'GET', null, $this->getAuthorizationHeader($token, [
            // 'Accept' => 'application/json',
        ]));

        return json_decode($response, true);
    }
}
