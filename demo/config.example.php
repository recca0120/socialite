<?php

function getApp($request)
{
    return [
        'request' => $request,
        'config' => [
            // OAuth1
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
            // OAuth2
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
            'services.instagram' => [
                'client_id' => '',
                'client_secret' => '',
                'redirect' => $request->url(),
            ],
            'services.linkedin' => [
                'client_id' => '',
                'client_secret' => '',
                'redirect' => $request->url(),
            ],
        ],
    ];
}
