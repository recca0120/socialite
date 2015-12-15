<?php

namespace Recca0120\Socialite;

use Illuminate\Support\ServiceProvider;

class SocialiteServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     */
    // public function boot()
    // {
    //     $this->publishes([
    //         __DIR__.'/../config/services.php' => config_path('services.php'),
    //     ]);
    // }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Recca0120\Socialite\Contracts\Factory', function ($app) {
            return new SocialiteManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Recca0120\Socialite\Contracts\Factory'];
    }
}
