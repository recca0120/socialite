<?php

namespace Recca0120\Socialite\OAuthLib\OAuth2;

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
     * @param bool $stateParameterInAutUrl
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

        $serviceAccountName = $this->credentials->getConsumerId();
        $privateKey = file_get_contents($this->credentials->getConsumerSecret());
        $privateKeyPassword = 'notasecret';
        $assertionType = 'http://oauth.net/grant_type/jwt/1.0/bearer';
        $sub = false;
        $useCache = true;

        $this->serviceAccountName = $serviceAccountName;
        // $this->scopes = implode($this->getScopesDelimiter(), $this->scopes);
        $this->privateKey = $privateKey;
        $this->privateKeyPassword = $privateKeyPassword;
        $this->assertionType = $assertionType;
        $this->sub = $sub;
        $this->prn = $sub;
        $this->useCache = $useCache;
    }

    public function requestAccessToken($code, $state = null)
    {
        $bodyParams = [
            'grant_type' => 'assertion',
            'assertion_type' => $this->assertionType,
            'assertion' => $this->generateAssertion(),
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

    /**
     * Refreshes an OAuth2 access token.
     *
     * @param TokenInterface $token
     *
     * @return TokenInterface $token
     *
     * @throws MissingRefreshTokenException
     */
    public function refreshAccessToken(TokenInterface $token)
    {
        return $this->requestAccessToken();
    }

    protected function generateAssertion()
    {
        $now = time();

        $jwtParams = [
            'aud' => $this->getAccessTokenEndpoint()->getAbsoluteUri(),
            'scope' => implode($this->getScopesDelimiter(), $this->scopes),
            'iat' => $now,
            'exp' => $now + self::MAX_TOKEN_LIFETIME_SECS,
            'iss' => $this->serviceAccountName,
        ];

        if ($this->sub !== false) {
            $jwtParams['sub'] = $this->sub;
        } elseif ($this->prn !== false) {
            $jwtParams['prn'] = $this->prn;
        }

        return $this->makeSignedJwt($jwtParams);
    }

    /**
     * Creates a signed JWT.
     * @param array $payload
     * @return string The signed JWT.
     */
    private function makeSignedJwt($payload)
    {
        $header = ['typ' => 'JWT', 'alg' => 'RS256'];

        $payload = json_encode($payload);

        // Handle some overzealous escaping in PHP json that seemed to cause some errors
        // with claimsets.
        $payload = str_replace('\/', '/', $payload);

        $segments = [
            static::urlSafeB64Encode(json_encode($header)),
            static::urlSafeB64Encode($payload),
        ];
        $signingInput = implode('.', $segments);
        $signature = static::p12Sign($this->privateKey, $this->privateKeyPassword, $signingInput);
        $segments[] = static::urlSafeB64Encode($signature);

        return implode('.', $segments);
    }

    public static function p12Sign($p12, $password, $data)
    {
        if (! function_exists('openssl_x509_read')) {
            throw new \Exception(
                'The Google PHP API library needs the openssl PHP extension'
            );
        }
        // If the private key is provided directly, then this isn't in the p12
        // format. Different versions of openssl support different p12 formats
        // and the key from google wasn't being accepted by the version available
        // at the time.
        if (! $password && strpos($p12, '-----BEGIN RSA PRIVATE KEY-----') !== false) {
            $privateKey = openssl_pkey_get_private($p12);
        } elseif ($password === 'notasecret' && strpos($p12, '-----BEGIN PRIVATE KEY-----') !== false) {
            $privateKey = openssl_pkey_get_private($p12);
        } else {
            // This throws on error
            $certs = [];
            if (! openssl_pkcs12_read($p12, $certs, $password)) {
                throw new \Exception(
                    'Unable to parse the p12 file.  '.
                    'Is this a .p12 file?  Is the password correct?  OpenSSL error: '.
                    openssl_error_string()
                );
            }
            // TODO(beaton): is this part of the contract for the openssl_pkcs12_read
            // method?  What happens if there are multiple private keys?  Do we care?
            if (! array_key_exists('pkey', $certs) || ! $certs['pkey']) {
                throw new \Exception('No private key found in p12 file.');
            }
            $privateKey = openssl_pkey_get_private($certs['pkey']);
        }
        if (! $privateKey) {
            throw new \Exception('Unable to load private key');
        }

        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            static::freeOpensslPkey($privateKey);
            throw new \Exception(
                'PHP 5.3.0 or higher is required to use service accounts.'
            );
        }
        $hash = defined('OPENSSL_ALGO_SHA256') ? OPENSSL_ALGO_SHA256 : 'sha256';
        if (! openssl_sign($data, $signature, $privateKey, $hash)) {
            static::freeOpensslPkey($privateKey);
            throw new \Exception('Unable to sign data');
        }
        static::freeOpensslPkey($privateKey);

        return $signature;
    }

    public static function freeOpensslPkey($privateKey)
    {
        if ($privateKey) {
            openssl_pkey_free($privateKey);
        }
    }

    public static function urlSafeB64Encode($data)
    {
        $b64 = base64_encode($data);
        $b64 = str_replace(['+', '/', '\r', '\n', '='], ['-', '_'], $b64);

        return $b64;
    }

    public static function urlSafeB64Decode($b64)
    {
        $b64 = str_replace(['-', '_'], ['+', '/'], $b64);

        return base64_decode($b64);
    }
}
