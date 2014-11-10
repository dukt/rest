<?php


namespace Craft;

use Guzzle\Http\Client;

class RestService extends BaseApplicationComponent
{
    /**
     * Get
     */
    public function get($requestHandle, $queryParams = null)
    {
        $request = $this->getRequestByHandle($requestHandle);

        // query

        if(!empty($queryParams))
        {
            $request->query = array_merge($request->query, $queryParams);
        }

        return $this->sendRequest($request);
    }

    /**
     * Request
     */
    public function request(array $params)
    {
        if(!empty($params['identity']))
        {

            $identity = $this->getIdentityByHandle($params['identity']);

            if($identity)
            {
                $params['identityId'] = $identity->id;
            }

            $params['identity'] = null;
        }

        $request = new Rest_RequestModel;

        $request->setAttributes($params);

        return $this->sendRequest($request);
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
     * Send Request
     */
    public function sendRequest(Rest_RequestModel $request)
    {
        $client = new Client();
        $options = array();
        $verb = 'get';
        $format = 'json';

        if(!empty($request->headers))
        {
            $options['headers'] = $request->headers;
        }

        if(!empty($request->query))
        {
            $options['query'] = $request->query;
        }

        if(!empty($request->verb))
        {
            $verb = $request->verb;
        }

        if(!empty($request->format))
        {
            $format = $request->format;
        }


        // identity

        $identity = null;

        if(!empty($request->identityId))
        {
            $identityId = $request->identityId;
            $identity = craft()->rest->getIdentityById($identityId);

            $providerHandle = $identity->providerHandle;
            $provider = craft()->oauth->getProvider($providerHandle);

            $tokenModel = $identity->getToken();

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
            $guzzleRequest = $client->{$verb}($request->url, array(), $options);

            $response = $guzzleRequest->send();

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

        $record->identityId = $model->identityId;
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
     * Get Identities
     */
    public function getIdentities()
    {
        $records = Rest_IdentityRecord::model()->findAll(array('order' => 't.id'));
        return Rest_IdentityModel::populateModels($records, 'id');
    }

    /**
     * Get Identity By ID
     */
    public function getIdentityById($id)
    {
        $record = Rest_IdentityRecord::model()->findByPk($id);

        if($record)
        {
            return Rest_IdentityModel::populateModel($record);
        }
    }

    /**
     * Save Identity
     */
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

    /**
     * Get Identity By Handle
     */
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

    /**
     * Delete Identity By ID
     */
    public function deleteIdentityById($id)
    {
        return Rest_IdentityRecord::model()->deleteByPk($id);
    }

    /**
     * Save Token
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
}
