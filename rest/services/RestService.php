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

use Guzzle\Http\Client;

class RestService extends BaseApplicationComponent
{
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

        // send request
        return $this->_sendRequest($criteria);
    }


    private function _sendRequest(Rest_RequestCriteriaModel $criteria)
    {
        $options = array();
        $baseUrl = null;


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


        $authentication = craft()->rest_authentications->getAuthenticationByHandle($criteria->authentication);

        if($authentication)
        {
            $oauthProvider = $authentication->getOAuthProvider();

            $client = $oauthProvider->getClient($authentication->getToken());
        }
        else
        {
            $client = new Client();
        }

        if(!empty($criteria->api))
        {
            $api = craft()->rest_apis->getApi($criteria->api);

            $client->setBaseUrl($api->getBaseUrl());
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

    private function _populateSavedRequest($criteria, $handle)
    {
        if(!empty($handle))
        {
            $request = craft()->rest_requests->getRequestByHandle($handle);

            if($request)
            {
                // update criteria from request

                $criteria->url = $request->url;
                $criteria->authenticationHandle = $request->authenticationHandle;

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

                    // case 'authenticationHandle':
                    // $criteria->api = $attributes['authenticationHandle'];
                    // break;

                    default:
                    $criteria->setAttribute($key, $value);
                }
            }
        }
    }
}