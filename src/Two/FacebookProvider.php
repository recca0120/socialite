<?php

namespace Recca0120\Socialite\Two;

use OAuth\Common\Consumer\Credentials;
use OAuth\OAuth2\Service\Facebook;
use OAuth\ServiceFactory;
use Recca0120\Socialite\Factory\Two as ProviderFactory;

class FacebookProvider extends ProviderFactory
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        Facebook::SCOPE_PUBLIC_PROFILE,
        Facebook::SCOPE_EMAIL,
    ];

    public $version = 'v2.4';

    public $graphUrl = 'https://graph.facebook.com';

    protected function createService(ServiceFactory $serviceFactory, Credentials $credentials)
    {
        $serviceFactory->registerService('facebook', '\Recca0120\Socialite\OAuthLib\OAuth2\Facebook');

        return $serviceFactory->createService(
            $this->driver,
            $credentials,
            $this->storage,
            $this->scopes,
            null,
            array_get($this->config, 'version', $this->version)
        );
    }

    public function getProfileUrl()
    {
        $fields = ['first_name', 'last_name', 'name', 'email', 'gender', 'verified'];

        return '/me?fields='.implode(',', $fields);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user, $extra = [])
    {
        $extra['avatar'] = $this->graphUrl.'/'.$this->version.'/'.array_get($user, 'id', 'me').'/picture';
        $extra['avatar_original'] = $extra['avatar'].'?width=1920';

        if (empty($user['name']) == true) {
            $extra['name'] = array_get($user, 'first_name').' '.array_get($user, 'last_name');
        }

        return parent::mapUserToObject($user, $extra);
    }
}
