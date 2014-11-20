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

namespace Craft;

use Guzzle\Http\Client;

class RestService extends BaseApplicationComponent
{
    /**
     * Request
     */
    public function request($attributes)
    {
        return new Rest_RequestCriteriaModel($attributes);
    }

    /**
     * Send Request
     */
    public function sendRequest(Rest_RequestCriteriaModel $criteria)
    {
        // get started from a saved request ?

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

                if($request->api)
                {
                    $criteria->api = $request->api;
                }
            }
            else
            {
                throw new Exception("Couldn't find request with handle ".$criteria->handle);
            }
        }


        // options

        $options = array();

        if(!empty($criteria->headers))
        {
            $options['headers'] = $criteria->headers;
        }

        if(!empty($criteria->query))
        {
            $options['query'] = $criteria->query;
        }


        // api

        $api = $this->getApiByHandle($criteria->api);


        // url

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

            if($api->getApiUrl())
            {
                $baseUrl = $api->getApiUrl();
            }
        }


        // client

        $client = new Client($baseUrl);


        // authenticate client

        $authentication = $this->getAuthenticationByHandle($criteria->api);

        if($authentication)
        {
            $providerHandle = $api->getProviderHandle();
            $provider = craft()->oauth->getProvider($providerHandle);

            $tokenModel = $authentication->getToken();

            if($tokenModel)
            {
                $token = $tokenModel->token;

                if($token)
                {
                    \Dukt\Rest\ClientFactory::authenticateClient($client, $provider, $token);
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
            $errorMsg = $e->getMessage();


            try {
                $data = @$e->getResponse()->{$criteria->format}(true);
            }
            catch(\Exception $e2)
            {
                $data = null;
            }

            return array(
                'success' => false,
                'data' => $data,
                'errorMsg' => $errorMsg
            );
        }
    }

    /**
     * Get APIs
     */
    public function getApis()
    {
        $apis = array();

        $path = CRAFT_PLUGINS_PATH.'rest/src/API/';
        $folderContents = IOHelper::getFolderContents($path, false);

        if($folderContents)
        {
            foreach($folderContents as $path)
            {
                $path = IOHelper::normalizePathSeparators($path);
                $fileName = IOHelper::getFileName($path, false);

                if($fileName == 'AbstractApi') continue;

                $class = $fileName;

                $apis[] = $this->_getApi($class);
            }
        }

        return $apis;
    }

    /**
     * Get API Instance
     */
    public function _getApi($class)
    {
        $fullClass = '\\Dukt\\Rest\\Api\\'.$class;
        return new $fullClass;
    }

    /**
     * Get API By Handle
     */
    public function getApiByHandle($handle)
    {
        $apis = $this->getApis();

        foreach($apis as $api)
        {
            if($api->getHandle() == $handle)
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


        $tokenModel->providerHandle = $api->getProviderHandle();
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