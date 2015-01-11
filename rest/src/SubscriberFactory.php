<?php

/**
* Craft REST by Dukt
 *
 * @package   Craft REST
 * @author    Benjamin David
 * @copyright Copyright (c) 2015, Dukt
 * @link      https://dukt.net/craft/rest/
 * @license   https://dukt.net/craft/rest/docs#license
 */

namespace Dukt\Rest;

class SubscriberFactory {

    public static function get($api, $client, $provider, $token)
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

                return $oauth;

                break;

            case 2:
                $config = array(
                    'consumer_key' => $provider->clientId,
                    'consumer_secret' => $provider->clientSecret,
                    'authorization_method' => $api->getAuthorizationMethod(),
                    'access_token' => $token->getAccessToken(),
                );

                $oauth = new \Dukt\Rest\Guzzle\Plugin\Oauth2Plugin($config);

                return $oauth;

                break;
        }
    }
}
