<?php

require __DIR__.'/../../../../vendor/autoload.php';
require __DIR__.'/config.php';

use Illuminate\Http\Request;
use Recca0120\Socialite\SocialiteManager;

session_start();

function getNextDriver($currentDriver, $drivers) {

}

$request = Request::capture();
$app = getApp($request);
$drivers = getDrivers($app['config']);
$showUser = empty($_GET['show']) === false;
// $driver = empty($_GET['next']) ? $drivers[0] : $_GET['next'];
// dump($drivers);
// OAuth1
// $driver = 'bitbucket';
$driver = 'twitter';
// OAuth2
// $driver = 'facebook';
// $driver = 'github';
// $driver = 'google';
// $driver = 'googleservice';
// $driver = 'instagram';
// $driver = 'linkedin';

$socialiteManager = new SocialiteManager($app);
$socialite = $socialiteManager
    ->driver($driver)
    ->stateless();

if ($driver === 'googleservice') {
    dump($socialite->service(), $socialite->scopes([
        'https://www.googleapis.com/auth/analytics.readonly',
    ])->getAccessToken());
} elseif ($showUser) {
    dump($socialite->service(), $socialite->user());
} elseif (isset($_GET['oauth_token']) === true){
    header('location: '.$request->url().'?show=1');
} elseif (isset($_GET['code']) === true) {
    // dump($socialite->user(), $_SESSION);
    header('location: '.$request->url().'?show=1');
} else {
    $response = $socialite->with([
    ])->redirect();
    $response->send();
}
