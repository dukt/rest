<?php

/**
* Craft REST by Dukt
 *
 * @package   Craft REST
 * @author    Benjamin David
 * @copyright Copyright (c) 2014, Dukt
 * @link      https://dukt.net/craft/rest/
 * @license   https://dukt.net/craft/rest/docs#license
 */

namespace Dukt\Rest;

class ClientFactory {

    public static function authenticateClient($client, $provider, $token)
    {
        $fullClassname = get_class($provider->source->service);

        $headers = array();
        $query = array();

        switch($fullClassname::OAUTH_VERSION)
        {
            case 1:
                $oauth = new \Guzzle\Plugin\Oauth\OauthPlugin(array(
                    'consumer_key'    => $provider->clientId,
                    'consumer_secret' => $provider->clientSecret,
                    'token'           => $token->getAccessToken(),
                    'token_secret'    => $token->getAccessTokenSecret()
                ));

                break;

            case 2:
                $query['access_token'] = $token->getAccessToken();
                break;
        }

        $client->setDefaultOption('headers', $headers);
        $client->setDefaultOption('query', $query);

        if(isset($oauth))
        {
            $client->addSubscriber($oauth);
        }
    }
}

/* OAUTH 1 Specs */

    /*

    $uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);

    $token = $this->storage->retrieveAccessToken($this->service());
    $extraHeaders = array_merge($this->getExtraApiHeaders(), $extraHeaders);
    $authorizationHeader = array(
        'Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $uri, $token, $body)
    );
    $headers = array_merge($authorizationHeader, $extraHeaders);

    return $this->httpClient->retrieveResponse($uri, $body, $headers, $method);

    */

/* OAuth 2 */

    /*

    $uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);
    $token = $this->storage->retrieveAccessToken($this->service());

    if ($token->getEndOfLife() !== TokenInterface::EOL_NEVER_EXPIRES
        && $token->getEndOfLife() !== TokenInterface::EOL_UNKNOWN
        && time() > $token->getEndOfLife()
    ) {
        throw new ExpiredTokenException(
            sprintf(
                'Token expired on %s at %s',
                date('m/d/Y', $token->getEndOfLife()),
                date('h:i:s A', $token->getEndOfLife())
            )
        );
    }

    // add the token where it may be needed
    if (static::AUTHORIZATION_METHOD_HEADER_OAUTH === $this->getAuthorizationMethod()) {
        $extraHeaders = array_merge(array('Authorization' => 'OAuth ' . $token->getAccessToken()), $extraHeaders);
    } elseif (static::AUTHORIZATION_METHOD_QUERY_STRING === $this->getAuthorizationMethod()) {
        $uri->addToQuery('access_token', $token->getAccessToken());
    } elseif (static::AUTHORIZATION_METHOD_QUERY_STRING_V2 === $this->getAuthorizationMethod()) {
        $uri->addToQuery('oauth2_access_token', $token->getAccessToken());
    } elseif (static::AUTHORIZATION_METHOD_QUERY_STRING_V3 === $this->getAuthorizationMethod()) {
        $uri->addToQuery('apikey', $token->getAccessToken());
    } elseif (static::AUTHORIZATION_METHOD_HEADER_BEARER === $this->getAuthorizationMethod()) {
        $extraHeaders = array_merge(array('Authorization' => 'Bearer ' . $token->getAccessToken()), $extraHeaders);
    }

    $extraHeaders = array_merge($this->getExtraApiHeaders(), $extraHeaders);

    return $this->httpClient->retrieveResponse($uri, $body, $extraHeaders, $method);

    */