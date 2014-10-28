<?php


namespace Craft;

use Guzzle\Http\Client;

class RestService extends BaseApplicationComponent
{

    /**
     * Save OAuth Token
     */
    public function saveToken($identity, $token)
    {
        // get tokenId
        $tokenId = $identity->tokenId;

        // get token
        $model = craft()->oauth->getTokenById($tokenId);


        // populate token model

        if(!$model)
        {
            $model = new Oauth_TokenModel;
        }

        $model->providerHandle = $identity->provider;
        $model->pluginHandle = 'rest';
        $model->encodedToken = craft()->oauth->encodeToken($token);

        // save token
        craft()->oauth->saveToken($model);

        // set token ID
        $identity->tokenId = $model->id;

        // save identity
        $this->saveIdentity($identity);
    }

    public function deleteIdentityById($id)
    {
        return Rest_IdentityRecord::model()->deleteByPk($id);
    }

    public function getIdentityById($id)
    {
        $record = Rest_IdentityRecord::model()->findByPk($id);

        if($record)
        {
            return Rest_IdentityModel::populateModel($record);
        }
    }

    public function getIdentities()
    {
        $records = Rest_IdentityRecord::model()->findAll(array('order' => 't.id'));
        return Rest_IdentityModel::populateModels($records, 'id');
    }

    public function saveIdentity(Rest_IdentityModel $model)
    {
        $record = Rest_IdentityRecord::model()->findByPk($model->id);

        if(!$record)
        {
            $record = new Rest_IdentityRecord;
        }

        $record->tokenId = $model->tokenId;
        $record->provider = $model->provider;
        $record->scopes = $model->scopes;
        $record->params = $model->params;

        return $record->save();
    }

    public function get($url, $options = array())
    {
        return $this->api($url, $options);
    }


    // todo: do we need to keep post fields

    public function api($url, $options = null)
    {
        $queryParams = array();
        $providerHandle = false;
        $tokenModel = false;

        // headers

        $headers = null;

        if(!empty($options['headers']))
        {
            $headers = $options['headers'];
        }

        // postFields

        $postFields = null;

        if(!empty($options['postFields']))
        {
            $postFields = $options['postFields'];
        }

        // identity

        $identity = null;

        if(!empty($options['identity']))
        {
            $identityId = $options['identity'];
            $identity = craft()->rest->getIdentityById($identityId);

            $providerHandle = $identity->provider;
            $tokenModel = $identity->getToken();
        }

        // client
        $client = new Client();

        if($providerHandle && $tokenModel)
        {
            $provider = craft()->oauth->getProvider($providerHandle);

            if($tokenModel)
            {
                $token = $tokenModel->token;
            }


            if($provider && $token)
            {

                switch ($provider->source->getOAuthVersion())
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
                        $oauth = new \Guzzle\Plugin\Oauth\OauthPlugin(array(
                            'consumer_key'    => $provider->clientId,
                            'consumer_secret' => $provider->clientSecret,
                            'token'           => $token->getAccessToken(),
                        ));

                        $queryParams['access_token'] = $token->getAccessToken();
                        break;
                }
                $client->addSubscriber($oauth);
            }
        }

        // build url

        if(count($queryParams) > 0)
        {
            if(strpos($url, "?") !== false)
            {
               $url = $url.'&'.http_build_query($queryParams);
            }
            else
            {
                $url = $url.'?'.http_build_query($queryParams);
            }
        }


        // send request

        try {
            $response = $client->get($url, $headers, $postFields)->send();

            // return json response as objects

            return array(
                'success' => true,
                'data' => $response->json()
            );
        }
        catch(\Exception $e)
        {
            return array(
                'success' => false,
                'error' => $e->getResponse()->getBody(true)
            );
        }
    }

    public function apiOAuth2($url, $queryParams = array(), $headers = null, $postFields = null)
    {
        // client
        $client = new Client();


        // authenticate client (oauth 2.0)
        $oauth = new \Guzzle\Plugin\Oauth\OauthPlugin(array(
            'consumer_key'    => $provider->clientId,
            'consumer_secret' => $provider->clientSecret,
            'token'           => $token->getAccessToken(),
        ));

        $client->addSubscriber($oauth);

        $queryParams['access_token'] = $token->getAccessToken();


        // build url
        $url = $url.'?'.http_build_query($queryParams);

        // send request
        $response = $client->get($url, $headers, $postFields)->send();

        // return json response as objects
        return $response->json();
    }
}
