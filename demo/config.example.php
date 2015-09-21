<?php

function getApp($request)
{
    $config = [
        // OAuth1
        'bitbucket' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => '',
        ],
        'twitter' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => '',
        ],
        // OAuth2
        'dropbox' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => '',
        ],
        'facebook' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => '',
        ],
        'github' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => '',
        ],
        'google' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => ''.'?/callback',
        ],
        'googleservice' => [
            'client_id' => '',
            'client_secret' => __DIR__.'/path_to.p12',
            'redirect' => '',
        ],
        'instagram' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => '',
        ],
        'linkedin' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => '',
        ],
    ];

    $configMap = [];
    foreach ($config as $key => $value) {
        $configMap['services.'.$key] = $value;
    }

    return [
        'request' => $request,
        'config' => $configMap,
    ];
}
