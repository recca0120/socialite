<?php

namespace Recca0120\Socialite\Contracts;

interface Factory
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param string $driver
     *
     * @return \Recca0120\Socialite\Contracts\Provider
     */
    public function driver($driver = null);
}
