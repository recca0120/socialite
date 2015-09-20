<?php

namespace Recca0120\Socialite\One;

use Illuminate\Http\Request;

class BitbucketProvider extends ProviderFactory
{
    protected $mapUserToObject = [
        'id' => 'user.username',
        'nickname' => 'user.username',
        'name' => 'user.display_name',
        'avatar' => 'user.avatar',
    ];

    public function getProfileUrl()
    {
        return '/user';
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user, $extra = [])
    {
        $service = $this->getService();

        $user['emails'] = json_decode($service->request('/users/'.array_get($user, 'user.username').'/emails'), true);
        $extra = [
            'email' => array_get($user, 'emails.0.email'),
        ];

        return parent::mapUserToObject($user, $extra);
    }
}
