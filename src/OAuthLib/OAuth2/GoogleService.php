<?php

namespace Recca0120\Socialite\OAuthLib\OAuth2;

use Firebase\JWT\JWT;
use InvalidArgumentException;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Exception\InvalidScopeException;
use OAuth\OAuth2\Service\Google;

class GoogleService extends Google
{
    const MAX_TOKEN_LIFETIME_SECS = 3600;

    /**
     * @param CredentialsInterface  $credentials
     * @param ClientInterface       $httpClient
     * @param TokenStorageInterface $storage
     * @param array                 $scopes
     * @param UriInterface|null     $baseApiUri
     * @param bool                  $stateParameterInAutUrl
     * @param string                $apiVersion
     *
     * @throws InvalidScopeException
     */
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = [],
        UriInterface $baseApiUri = null,
        $stateParameterInAutUrl = false,
        $apiVersion = ''
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri, $stateParameterInAutUrl, $apiVersion);

        $consumerSecret = $this->credentials->getConsumerSecret();
        if (is_file($consumerSecret) === true) {
            $extension = substr($consumerSecret, strrpos($consumerSecret, '.') + 1);
            $privateyKey = file_get_contents($consumerSecret);
            switch ($extension) {
                case 'p12':
                    $this->privateKey = $this->getPrivateKeyFromPKCS12($privateyKey, 'notasecret');
                    break;
                case 'json':
                    $this->privateKey = json_decode($privateyKey, true)['private_key'];
                    break;
            }
        } else {
            $this->privateKey = $consumerSecret;
        }
    }

    public function requestAccessToken($code, $state = null)
    {
        $bodyParams = [
            'grant_type'     => 'assertion',
            'assertion_type' => 'http://oauth.net/grant_type/jwt/1.0/bearer',
            'assertion'      => $this->generateAssertion(),
        ];

        $responseBody = $this->httpClient->retrieveResponse(
            $this->getAccessTokenEndpoint(),
            $bodyParams,
            $this->getExtraOAuthHeaders()
        );

        $token = $this->parseAccessTokenResponse($responseBody);
        $this->storage->storeAccessToken($this->service(), $token);

        return $token;
    }

    public function refreshAccessToken(TokenInterface $token)
    {
        return $this->requestAccessToken('');
    }

    protected function generateAssertion()
    {
        $now = time();
        $payload = [
            'aud'   => $this->getAccessTokenEndpoint()->getAbsoluteUri(),
            'scope' => implode($this->getScopesDelimiter(), $this->scopes),
            'iat'   => $now,
            'exp'   => $now + self::MAX_TOKEN_LIFETIME_SECS,
            'iss'   => $this->credentials->getConsumerId(),
        ];

        $sub = false;
        $prn = $sub;

        if ($sub !== false) {
            $jwtParams['sub'] = $this->sub;
        } elseif ($prn !== false) {
            $jwtParams['prn'] = $this->prn;
        }

        return JWT::encode($payload, $this->privateKey, 'RS256');
    }

    // Creates a new signer from a .p12 file.

    public function getPrivateKeyFromPKCS12($p12, $password)
    {
        if (!function_exists('openssl_x509_read')) {
            throw new InvalidArgumentException(
                'The library needs the openssl PHP extension'
            );
        }

        // If the private key is provided directly, then this isn't in the p12
        // format. Different versions of openssl support different p12 formats
        // and the key from google wasn't being accepted by the version available
        // at the time.
        if (!$password && strpos($p12, '-----BEGIN RSA PRIVATE KEY-----') !== false) {
            $privateKey = openssl_pkey_get_private($p12);
        } elseif ($password === 'notasecret' && strpos($p12, '-----BEGIN PRIVATE KEY-----') !== false) {
            $privateKey = openssl_pkey_get_private($p12);
        } else {
            // This throws on error
            $certs = [];
            if (!openssl_pkcs12_read($p12, $certs, $password)) {
                throw new InvalidArgumentException(
                    'Unable to parse the p12 file.  '.
                    'Is this a .p12 file?  Is the password correct?  OpenSSL error: '.
                    openssl_error_string()
                );
            }
            // TODO(beaton): is this part of the contract for the openssl_pkcs12_read
            // method?  What happens if there are multiple private keys?  Do we care?
            if (!array_key_exists('pkey', $certs) || !$certs['pkey']) {
                throw new InvalidArgumentException('No private key found in p12 file.');
            }
            $privateKey = openssl_pkey_get_private($certs['pkey']);
        }

        if (!$privateKey) {
            throw new InvalidArgumentException('Unable to load private key');
        }

        $out = openssl_pkey_get_details($privateKey);
        openssl_pkey_free($privateKey);

        return $out['key'];
    }
}
