<?php

namespace Recca0120\Socialite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Recca0120\Socialite\SocialiteManager
 */
class Socialite extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Recca0120\Socialite\Contracts\Factory';
    }
}
