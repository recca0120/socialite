<?php

use Illuminate\Http\Request;

$driver = 'google';
$request = Request::capture();
$app = [
    'request' => $request,
    'config' => [
        'services.google' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => $request->url().'?/callback',
        ],
        'services.facebook' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => $request->url(),
        ],
        'services.github' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => $request->url(),
        ],
        'services.linkedin' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => $request->url(),
        ],
        'services.instagram' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => $request->url(),
        ],
        'services.bitbucket' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => $request->url(),
        ],
        'services.twitter' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => $request->url(),
        ],
    ],
];
