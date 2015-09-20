<?php

namespace Recca0120\Socialite\Factory\Traits;

trait MapUserToObject
{
    protected $mapUserToObject = [
        'id' => 'id',
        'nickname' => 'nickname',
        'name' => 'name',
        'email' => 'email',
        'avatar' => 'picture',
    ];

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user, $extra = [])
    {
        $mapUserToObject = $extra;
        foreach ($this->mapUserToObject as $key => $value) {
            if (isset($mapUserToObject[$key]) === false) {
                $mapUserToObject[$key] = array_get($user, $value);
            }
        }

        return $this->getUserObject()->setRaw($user)->map($mapUserToObject);
    }
}
