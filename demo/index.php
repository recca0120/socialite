<?php

require __DIR__.'/../../../autoload.php';
require __DIR__.'/config.php';

use Illuminate\Http\Request;
use Recca0120\Socialite\SocialiteManager;

$request = Request::capture();
$driver = 'googleservice';
$app = getApp($request);

$socialiteManager = new SocialiteManager($app);
$socialite = $socialiteManager
    ->driver($driver)
    ->stateless();
if ($driver === 'googleservice') {
    dump($socialite->scopes([
        'https://www.googleapis.com/auth/analytics.readonly'
    ])->getAccessToken());
} else if (isset($_GET['oauth_token']) === true) {
    dump($socialite->user());
} elseif (isset($_GET['code']) === true) {
    dump($socialite->user());
} else {
    $response = $socialite->with([
    ])->redirect();
    $response->send();
}
