<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/config.php';

use Illuminate\Http\Request;
use Recca0120\Socialite\SocialiteManager;

session_start();

$request = Request::capture();
$app = getApp($request);
$drivers = getDrivers($app['config']);
$showUser = empty($_GET['show']) === false;
// $driver = empty($_GET['next']) ? $drivers[0] : $_GET['next'];
// dump($drivers);
// OAuth1
// $driver = 'bitbucket';
// $driver = 'twitter';
// OAuth2
// $driver = 'dropbox';
$driver = 'facebook';
// $driver = 'github';
// $driver = 'google';
// $driver = 'googleservice';
// $driver = 'instagram';
// $driver = 'linkedin';

$socialiteManager = new SocialiteManager($app);
$socialite = $socialiteManager
    ->driver($driver);
    // ->stateless();

if ($driver === 'googleservice') {
    dump($socialite->scopes([
        'https://www.googleapis.com/auth/analytics.readonly',
    ])->getAccessToken());
} elseif ($showUser) {
    dump($socialite->user(), $socialite->service());
} elseif (isset($_GET['oauth_token']) === true) {
    // $socialite->getAccessToken();
    // header('location: '.$request->url().'?show=1');
    dump($socialite->user(), $_SESSION);
} elseif (isset($_GET['code']) === true) {
    // $socialite->getAccessToken();
    // header('location: '.$request->url().'?show=1');
    dump($socialite->user(), $_SESSION);
} else {
    // $socialite->with([
    // ]);

    $response = $socialite->stateless()->redirect();
    $response->send();
}
