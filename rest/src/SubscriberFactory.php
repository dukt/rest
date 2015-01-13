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

    public static function get($api, $providerSource)
    {
        $headers = array();
        $query = array();

        $provider = $providerSource->getProvider();
        $realToken = $providerSource->getRealToken();

        switch($providerSource->oauthVersion)
        {
            case 1:
                $oauth = new \Guzzle\Plugin\Oauth\OauthPlugin(array(
                    'consumer_key'    => $provider->clientId,
                    'consumer_secret' => $provider->clientSecret,
                    'token'           => $realToken->getAccessToken(),
                    'token_secret'    => $realToken->getAccessTokenSecret()
                ));

                return $oauth;

                break;

            case 2:
                $config = array(
                    'consumer_key' => $provider->clientId,
                    'consumer_secret' => $provider->clientSecret,
                    'authorization_method' => $api->getAuthorizationMethod(),
                    'access_token' => $realToken->getAccessToken(),
                );

                $oauth = new \Dukt\Rest\Guzzle\Plugin\Oauth2Plugin($config);

                return $oauth;

                break;
        }
    }
}
