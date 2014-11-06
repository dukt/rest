<?php


namespace Craft;

use Guzzle\Http\Client;

class RestService extends BaseApplicationComponent
{
    public function get($requestHandle, $queryParams = null)
    {
        $request = $this->getRequestByHandle($requestHandle);

        $url = $request->url;

        $options = array();


        // params

        $options['params'] = array();

        if(!empty($request->params))
        {
            $options['params'] = $request->params;
        }

        if(!empty($queryParams))
        {
            $options['params'] = array_merge($options['params'], $queryParams);
        }


        // identity

        if(!empty($request->identityId))
        {
            $options['identity'] = $request->identityId;
        }


        // verb

        if(!empty($request->verb))
        {
            $options['verb'] = $request->verb;
        }


        // format

        if(!empty($request->format))
        {
            $options['format'] = $request->format;
        }

        return $this->api($url, $options);
    }

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

        $model->providerHandle = $identity->providerHandle;
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

    public function deleteRequestById($id)
    {
        return Rest_RequestRecord::model()->deleteByPk($id);
    }

    public function getIdentityById($id)
    {
        $record = Rest_IdentityRecord::model()->findByPk($id);

        if($record)
        {
            return Rest_IdentityModel::populateModel($record);
        }
    }

    public function getRequestById($id)
    {
        $record = Rest_RequestRecord::model()->findByPk($id);

        if($record)
        {
            return Rest_RequestModel::populateModel($record);
        }
    }

    public function getIdentityByHandle($handle)
    {
        $record = Rest_IdentityRecord::model()->find(
            array(
                'condition' => 'handle=:handle',
                'params' => array(':handle' => $handle)
            )
        );

        if($record)
        {
            return Rest_IdentityModel::populateModel($record);
        }
    }

    public function getRequestByHandle($handle)
    {
        $record = Rest_RequestRecord::model()->find(
            array(
                'condition' => 'handle=:handle',
                'params' => array(':handle' => $handle)
            )
        );

        if($record)
        {
            return Rest_RequestModel::populateModel($record);
        }
    }

    public function getIdentities()
    {
        $records = Rest_IdentityRecord::model()->findAll(array('order' => 't.id'));
        return Rest_IdentityModel::populateModels($records, 'id');
    }

    public function getRequests()
    {
        $records = Rest_RequestRecord::model()->findAll(array('order' => 't.id'));
        return Rest_RequestModel::populateModels($records, 'id');
    }

    public function saveIdentity(Rest_IdentityModel $model)
    {
        $record = Rest_IdentityRecord::model()->findByPk($model->id);

        if(!$record)
        {
            $record = new Rest_IdentityRecord;
        }

        $record->tokenId = $model->tokenId;
        $record->name = $model->name;
        $record->handle = $model->handle;
        $record->providerHandle = $model->providerHandle;
        $record->scopes = $model->scopes;
        $record->params = $model->params;

        $recordValidates = $record->validate();

        if ($recordValidates)
        {
            $record->save(false);

            if (!$model->id)
            {
                $model->id = $record->id;
            }

            return true;
        }
        else
        {
            $model->addErrors($record->getErrors());
            return false;
        }

        return $record->save();
    }

    public function request(array $options)
    {
        if(!empty($options['identity']))
        {
            $identity = $this->getIdentityByHandle($options['identity']);

            if($identity)
            {
                $options['identityId'] = $identity->id;
            }

            $options['identity'] = null;
        }

        $request = new Rest_RequestModel;

        $request->setAttributes($options);

        return $this->sendRequest($request);
    }

    public function sendRequest(Rest_RequestModel $request)
    {
        $url = $request->url;


        $options = array();

        if(!empty($request->params))
        {
            $options['params'] = $request->params;
        }

        if(!empty($request->identityId))
        {
            $options['identity'] = $request->identityId;
        }

        if(!empty($request->verb))
        {
            $options['verb'] = $request->verb;
        }

        if(!empty($request->format))
        {
            $options['format'] = $request->format;
        }

        return $this->api($url, $options);
    }

    public function saveRequest(Rest_RequestModel $model)
    {
        $record = Rest_RequestRecord::model()->findByPk($model->id);

        if(!$record)
        {
            $record = new Rest_RequestRecord;
        }

        $record->identityId = $model->identityId;
        $record->name = $model->name;
        $record->handle = $model->handle;
        $record->verb = $model->verb;
        $record->format = $model->format;
        $record->url = $model->url;
        $record->params = $model->params;

        if($record->save())
        {
            $model->setAttribute('id', $record->getAttribute('id'));
            return true;
        }
        else
        {
            $model->addErrors($record->getErrors());
            return false;
        }
    }

    public function api($url, $options = array())
    {
        $queryParams = array();
        $providerHandle = false;
        $tokenModel = false;
        $verb = 'get';
        $format = 'json';

        // verb

        if(!empty($options['verb']))
        {
            $verb = $options['verb'];
        }

        // format

        if(!empty($options['format']))
        {
            $format = $options['format'];
        }



        // queryParams

        if(!empty($options['params']))
        {
            $params = $options['params'];
        }

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

            $providerHandle = $identity->providerHandle;
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
                $classname = get_class($provider->source->service);

                switch($classname::OAUTH_VERSION)
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

                        $params['access_token'] = $token->getAccessToken();
                        break;
                }
                $client->addSubscriber($oauth);
            }
        }

        // build url

        if(isset($params) && count($params) > 0)
        {
            if(strpos($url, "?") !== false)
            {
               $url = $url.'&'.http_build_query($params);
            }
            else
            {
                $url = $url.'?'.http_build_query($params);
            }
        }

        var_dump($url);

        // send request

        try {
            $response = $client->{$verb}($url, $headers, $postFields)->send();

            // return json response as objects

            return array(
                'success' => true,
                'data' => $response->{$format}()
            );
        }
        catch(\Exception $e)
        {
            return array(
                'success' => false,
                'data' => $e->getResponse()->{$format}(true)
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
