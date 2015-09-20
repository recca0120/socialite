# Laravel Socialite

## Introduction

Simulation Laravel Socialite

## Installation

```
composer require recca0120/socialite
```

### Laravel 5.0:

Update config/app.php
```php
'providers' => [
    ...
    'Recca0120\Socialite\SocialiteServiceProvider',
];
```

### Laravel 5.1:

Update config/app.php
```php
'providers' => [
    ...
    Recca0120\Socialite\SocialiteServiceProvider::class,
];
```

## Official Documentation

Documentation for Socialite can be found on the [Laravel website](http://laravel.com/docs/authentication#social-authentication).

### License

Laravel Socialite is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

Included service implementations
--------------------------------
- OAuth1
    - BitBucket
    - Twitter
- OAuth2
    - Facebook
    - GitHub
    - Google
    - Instagram
    - LinkedIn
