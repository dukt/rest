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

namespace Craft;

require_once(CRAFT_PLUGINS_PATH.'rest/vendor/autoload.php');

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
        // remember current attributes
        $attributes = $criteria->getAttributes();
        $requestHandle = $criteria->handle;

        // reset criteria to default values
        $criteria->url = null;
        $criteria->headers = null;
        $criteria->query = null;
        $criteria->verb = 'get';
        $criteria->format = 'json';

        // set from saved request
        $this->_populateSavedRequest($criteria, $requestHandle);

        // set passed attributes
        $this->_populateAttributes($criteria, $attributes);

        // api

        $api = $this->getApiByHandle($criteria->api);

        if($api)
        {
            // request is based on an API so we need to re-populate criteria
            // from scratch, beginning with default values from api (if any)

            $criteria->url = null;
            $criteria->headers = null;
            $criteria->query = $api->getDefaultQuery();
            $criteria->verb = 'get';
            $criteria->format = 'json';

            // set from saved request
            $this->_populateSavedRequest($criteria, $requestHandle);

            // set passed attributes
            $this->_populateAttributes($criteria, $attributes);
        }

        // send request
        return $this->_sendRequest($criteria);
    }

    private function _populateSavedRequest($criteria, $handle)
    {
        if(!empty($handle))
        {
            $request = $this->getRequestByHandle($handle);

            if($request)
            {
                // update criteria from request

                $criteria->url = $request->url;

                $this->_populateAttributes($criteria, $request->getAttributes());

                if(strpos($criteria->url, 'http://') === false && strpos($criteria->url, 'https://') === false)
                {
                    // $criteria->url = $request->url.$attributes['url'];
                    $criteria->url = $request->url;
                }
            }
            else
            {
                throw new Exception("Couldn't find request with handle: ".$handle);
            }
        }
    }

    private function _populateAttributes($criteria, $attributes)
    {
        foreach($attributes as $key => $value)
        {
            if($value)
            {
                switch($key)
                {
                    case 'headers':
                    case 'query':

                    if(is_array($criteria->{$key}) && is_array($attributes[$key]))
                    {
                        $criteria->setAttribute($key, array_merge($criteria->{$key}, $attributes[$key]));
                    }
                    elseif(is_array($attributes[$key]))
                    {
                        $criteria->setAttribute($key, $attributes[$key]);
                    }

                    break;

                    case 'apiHandle':
                    $criteria->api = $attributes['apiHandle'];
                    break;

                    default:
                    $criteria->setAttribute($key, $value);
                }
            }
        }
    }

    private function _sendRequest(Rest_RequestCriteriaModel $criteria)
    {
        $options = array();
        $baseUrl = null;

        $api = $this->getApiByHandle($criteria->api);

        // headers

        if(!empty($criteria->headers))
        {
            $options['headers'] = $criteria->headers;
        }

        // query

        if(is_array($criteria->query))
        {
            $options['query'] = $criteria->query;
        }


        // api

        if($api && $api->getApiUrl())
        {
            if(strpos($criteria->url, 'http://') === false && strpos($criteria->url, 'https://') === false)
            {
                $baseUrl = $api->getApiUrl();
            }
        }


        // client
        $client = new Client($baseUrl);


        // authentication

        $authentication = $this->getAuthenticationByHandle($criteria->api);

        if($api && $authentication)
        {
            $this->checkRequirements();

            $providerHandle = $api->getProviderHandle();
            $provider = craft()->oauth->getProvider($providerHandle);

            $token = $authentication->getToken();

            if($token)
            {
                // set token
                $provider->setToken($token);

                // subscriber
                $oauthSubscriber = $provider->getSubscriber();
                $client->addSubscriber($oauthSubscriber);
            }
        }


        // send request

        try
        {
            // force get request
            $criteria->verb = 'get';

            // perform request

            $guzzleRequest = $client->{$criteria->verb}($criteria->url, array(), $options);

            $response = $guzzleRequest->send();

            $data = $response->{$criteria->format}();

            return array(
                'success' => true,
                'data' => $data
            );
        }
        catch(\Guzzle\Http\Exception\ClientErrorResponseException $e)
        {
            $errorMsg = $e->getMessage();
            $data = null;

            try
            {
                $data = $e->getResponse()->{$criteria->format}(true);
            }
            catch(\Exception $e2)
            {
                // couldn't get error data
            }

            return array(
                'success' => false,
                'data' => $data,
                'errorMsg' => $errorMsg,
            );
        }
        catch(\Exception $e)
        {
            $errorMsg = $e->getMessage();

            return array(
                'success' => false,
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

        $path = CRAFT_PLUGINS_PATH.'rest/src/Api/';
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
    public function getAuthenticationByHandle($apiHandle)
    {
        $record = Rest_AuthenticationRecord::model()->find(
            array(
                'condition' => 'apiHandle=:apiHandle',
                'params' => array(':apiHandle' => $apiHandle)
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
    public function saveAuthenticationToken($apiHandle, $token)
    {
        $this->checkRequirements();

        // get authentication

        $authentication = $this->getAuthenticationByHandle($apiHandle);

        if(!$authentication)
        {
            $authentication = new Rest_AuthenticationModel;
        }


        // get api

        $api = $this->getApiByHandle($apiHandle);


        // save token

        $token->id = $authentication->tokenId;
        $token->providerHandle = $api->getProviderHandle();
        $token->pluginHandle = 'rest';

        craft()->oauth->saveToken($token);


        // save authentication

        $authentication->apiHandle = $apiHandle;
        $authentication->tokenId = $token->id;

        $this->saveAuthentication($authentication);
    }

    /**
     * Delete Authentication By ID
     */
    public function deleteAuthenticationById($id)
    {
        $this->checkRequirements();

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

        $record->apiHandle = $model->apiHandle;
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

        $record->apiHandle = $model->apiHandle;
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

    /**
     * Check Requirements
     */
    public function checkRequirements()
    {
        $plugin = craft()->plugins->getPlugin('rest');

        $pluginDependencies = $plugin->getPluginDependencies();

        if(count($pluginDependencies) > 0)
        {
            throw new \Exception("REST is not configured properly. Check REST settings for more informations.");
        }
    }
}