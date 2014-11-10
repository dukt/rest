<?php

namespace Dukt\Rest\Client;

abstract class AbstractClient {
    public function getClient($client, $provider, $token)
    {
        $fullClassname = get_class($provider->source->service);
        $className = '\\Dukt\\Rest\\Client\\'.substr($fullClassname, strrpos($fullClassname, "\\") + 1);

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

        $client = new \Guzzle\Http\Client();

        $client->setDefaultOption('headers', $headers);
        $client->setDefaultOption('query', $query);

        if(isset($oauth))
        {
            $client->addSubscriber($oauth);
        }

        return $client;
    }
}
