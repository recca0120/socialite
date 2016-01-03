<?php

namespace Recca0120\Socialite\OAuthLib\OAuth2;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth2\Service\Facebook as baseFacebook;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Facebook extends baseFacebook
{
    protected function parseAccessTokenResponse($responseBody)
    {
        if (version_compare($this->apiVersion, 'v2.2', '>') === true) {
            $data = json_decode($responseBody, true);
        } else {
            parse_str($responseBody, $data);
        }

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            if (is_array($data['error']) === true) {
                $errorMessage = $data['error']['message'];
            } else {
                $errorMessage = $data['error'];
            }
            throw new TokenResponseException('Error in retrieving token: "'.$errorMessage.'"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);

        if (isset($data['expires'])) {
            $token->setLifeTime($data['expires']);
        }

        if (isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }

        unset($data['access_token']);
        unset($data['expires']);

        $token->setExtraParams($data);

        return $token;
    }
}
