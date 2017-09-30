<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
 */

namespace Craft;

use Guzzle\Http\Client;

require_once(CRAFT_PLUGINS_PATH.'rest/base/RestTrait.php');

class RestService extends BaseApplicationComponent
{
    // Traits
	// =========================================================================

	use RestTrait;
    
    // Public Methods
    // =========================================================================

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

        // set from saved request
        $this->_populateSavedRequest($criteria, $requestHandle);

        // set passed attributes
        $this->_populateAttributes($criteria, $attributes);

        // send request
        return $this->_sendRequest($criteria);
    }
	
    // Public Methods
    // =========================================================================

    private function _sendRequest(Rest_RequestCriteriaModel $criteria)
    {
        $options = array();
        $baseUrl = null;
        $method = 'get';


        // method

        if(!empty($criteria->method))
        {
            $method = $criteria->method;
        }


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


        // client

        $client = new Client();


        // authentication

        $authentication = craft()->rest_authentications->getAuthenticationByHandle($criteria->authentication);

        if($authentication)
        {
            if($authentication->tokenId)
            {
                $oauthProvider = $authentication->getOauthProvider();
                $token = $authentication->getToken();

                if($oauthProvider && $token)
                {
                    $subscriber = $oauthProvider->getSubscriber($token);
                    $client->addSubscriber($subscriber);
                }
            }
        }


        // token

        if(!empty($criteria->token))
        {
            $token = $criteria->token;
            $oauthProvider = $token->getProvider();
            $subscriber = $oauthProvider->getSubscriber($token);
            $client->addSubscriber($subscriber);
        }


        // send request

        try
        {
            // perform request


            if($method == 'delete')
            {
                $guzzleRequest = $client->{$method}($criteria->url, null, null, $options);
            }
            else
            {
                $guzzleRequest = $client->{$method}($criteria->url, array(), $options);
            }

            $response = $guzzleRequest->send();
            $contentType = $response->getContentType();

            $data = $response;

            if($data->getBody())
            {
                if(stripos($contentType, 'json') !== false)
                {
                    $data = $data->json();
                }
                elseif(stripos($contentType, 'xml') !== false)
                {
                    $data = $data->xml();
                }
            }

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
                $response = $e->getResponse();
                $contentType = $response->getContentType();

                $data = $response;

                if($data->getBody())
                {
                    if(stripos($contentType, 'json') !== false)
                    {
                        $data = $data->json();
                    }
                    elseif(stripos($contentType, 'xml') !== false)
                    {
                        $data = $data->xml();
                    }
                }
            }
            catch(\Exception $e2)
            {
                // todo: improve error handling when couldn't get error data
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
                $criteria->authentication = $request->authenticationHandle;

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

                    default:
                        $criteria->setAttribute($key, $value);
                }
            }
        }
    }
}
