<?php


namespace Craft;

use Guzzle\Http\Client;

class RestService extends BaseApplicationComponent
{
    /**
     * Request
     */
    public function request($attributes)
    {
        $criteria = new Rest_RequestCriteriaModel($attributes);

        return $criteria;
    }

    /**
     * Send Request
     */
    public function sendRequest(Rest_RequestCriteriaModel $criteria)
    {
        if($criteria->handle)
        {
            $request = $this->getRequestByHandle($criteria->handle);

            if($request)
            {
                $criteria->url = $request->url;
                $criteria->headers = $request->headers;
                $criteria->query = $request->query;
                $criteria->verb = $request->verb;
                $criteria->format = $request->format;

                if($request->authentication)
                {
                    $criteria->api = $request->authentication->handle;
                }
            }
            else
            {
                throw new Exception("Couldn't find request with handle ".$criteria->handle);

            }
        }

        $options = array();

        if(!empty($criteria->headers))
        {
            $options['headers'] = $criteria->headers;
        }

        if(!empty($criteria->query))
        {
            $options['query'] = $criteria->query;
        }


        // c

        $c = $this->getApiByHandle($criteria->api);


        // baseUrl & url

        $baseUrl = null;
        $url = null;
        $uri = null;

        if(strpos($criteria->url, 'http://') === 0 || strpos($criteria->url, 'https://') === 0)
        {
            $baseUrl = $criteria->url;
        }
        else
        {
            $url = $criteria->url;

            if(!empty($c['apiUrl']))
            {
                $baseUrl = $c['apiUrl'];
            }
        }


        // client

        $client = new Client($baseUrl);


        // authentication

        $authentication = $this->getAuthenticationByHandle($criteria->api);

        if($authentication)
        {
            $providerHandle = $c['providerHandle'];
            $provider = craft()->oauth->getProvider($providerHandle);

            $tokenModel = $authentication->getToken();

            if($tokenModel)
            {
                $token = $tokenModel->token;

                if($token)
                {
                    // authenticate client

                    $client = \Dukt\Rest\ApiFactory::getClient($client, $provider, $token);
                }
            }
        }



        // send request

        try
        {
            $guzzleRequest = $client->{$criteria->verb}($criteria->uri, array(), $options);

            $response = $guzzleRequest->send();

            return array(
                'success' => true,
                'data' => $response->{$criteria->format}()
            );
        }
        catch(\Exception $e)
        {
            return array(
                'success' => false,
                'data' => $e->getResponse()->{$criteria->format}(true)
            );
        }
    }

    /**
     * Get APIs
     */
    public function getApis()
    {
        $path = CRAFT_PLUGINS_PATH.'rest/data/apis.json';
        $json = file_get_contents($path);
        return json_decode($json, true);
    }

    /**
     * Get API By Handle
     */
    public function getApiByHandle($handle)
    {
        $apis = $this->getApis();

        foreach($apis as $api)
        {
            if($api['handle'] == $handle)
            {
                return $api;
            }
        }
    }

    /**
     * Get Authentication By Handle
     */
    public function getAuthenticationByHandle($handle)
    {
        $record = Rest_AuthenticationRecord::model()->find(
            array(
                'condition' => 'handle=:handle',
                'params' => array(':handle' => $handle)
            )
        );

        if($record)
        {
            return Rest_AuthenticationModel::populateModel($record);
        }
    }

    /**
     * Get Authentication By ID
     */
    public function getAuthenticationById($id)
    {
        $record = Rest_AuthenticationRecord::model()->findByPk($id);

        if($record)
        {
            return Rest_AuthenticationModel::populateModel($record);
        }
    }

    /**
     * Save Authentication Token
     */
    public function saveAuthenticationToken($handle, $token)
    {
        // get authentication

        $authentication = $this->getAuthenticationByHandle($handle);

        if(!$authentication)
        {
            $authentication = new Rest_AuthenticationModel;
        }


        // get api

        $api = $this->getApiByHandle($handle);


        // save token

        $tokenModel = craft()->oauth->getTokenById($authentication->tokenId);

        if(!$tokenModel)
        {
            $tokenModel = new Oauth_TokenModel;
        }


        $tokenModel->providerHandle = $api['providerHandle'];
        $tokenModel->pluginHandle = 'rest';
        $tokenModel->encodedToken = craft()->oauth->encodeToken($token);

        craft()->oauth->saveToken($tokenModel);


        // save authentication

        $authentication->handle = $handle;
        $authentication->tokenId = $tokenModel->id;

        $this->saveAuthentication($authentication);
    }

    /**
     * Delete Authentication By ID
     */
    public function deleteAuthenticationById($id)
    {
        $authentication = $this->getAuthenticationById($id);


        // delete token

        if($authentication->tokenId)
        {
            $token = craft()->oauth->getTokenById($authentication->tokenId);

            if($token)
            {
                craft()->oauth->deleteToken($token);
            }
        }

        return Rest_AuthenticationRecord::model()->deleteByPk($id);
    }

    /**
     * Get Authentications
     */
    public function getAuthentications()
    {
        $records = Rest_AuthenticationRecord::model()->findAll(array('order' => 't.id'));
        return Rest_AuthenticationModel::populateModels($records, 'id');
    }

    /**
     * Save Authentication
     */
    public function saveAuthentication(Rest_AuthenticationModel $model)
    {
        $record = Rest_AuthenticationRecord::model()->findByPk($model->id);

        if(!$record)
        {
            $record = new Rest_AuthenticationRecord;
        }

        $record->handle = $model->handle;
        $record->tokenId = $model->tokenId;

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

    /**
     * Get Requests
     */
    public function getRequests()
    {
        $records = Rest_RequestRecord::model()->findAll(array('order' => 't.id'));
        return Rest_RequestModel::populateModels($records, 'id');
    }

    /**
     * Get Request By ID
     */
    public function getRequestById($id)
    {
        $record = Rest_RequestRecord::model()->findByPk($id);

        if($record)
        {
            return Rest_RequestModel::populateModel($record);
        }
    }

    /**
     * Get Request By Handle
     */
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

    /**
     * Save Request
     */
    public function saveRequest(Rest_RequestModel $model)
    {
        $record = Rest_RequestRecord::model()->findByPk($model->id);

        if(!$record)
        {
            $record = new Rest_RequestRecord;
        }

        $record->api = $model->api;
        $record->name = $model->name;
        $record->handle = $model->handle;
        $record->verb = $model->verb;
        $record->format = $model->format;
        $record->url = $model->url;
        $record->headers = $model->headers;
        $record->query = $model->query;

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

    /**
     * Delete Request By ID
     */
    public function deleteRequestById($id)
    {
        return Rest_RequestRecord::model()->deleteByPk($id);
    }
}