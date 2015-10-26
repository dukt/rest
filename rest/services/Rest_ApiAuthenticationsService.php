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

class Rest_ApiAuthenticationsService extends BaseApplicationComponent
{
    /**
     * Get ApiAuthentication By Handle
     */
    public function getApiAuthenticationByHandle($apiHandle)
    {
        $record = Rest_ApiAuthenticationRecord::model()->find(
            array(
                'condition' => 'apiHandle=:apiHandle',
                'params' => array(':apiHandle' => $apiHandle)
            )
        );

        if($record)
        {
            return Rest_ApiAuthenticationModel::populateModel($record);
        }
    }

    /**
     * Get ApiAuthentication By Handle
     */
    public function _getApiAuthenticationRecordByHandle($apiHandle)
    {
        return Rest_ApiAuthenticationRecord::model()->find(
            array(
                'condition' => 'apiHandle=:apiHandle',
                'params' => array(':apiHandle' => $apiHandle)
            )
        );
    }

    /**
     * Get ApiAuthentication By ID
     */
    public function getApiAuthenticationById($id)
    {
        $record = Rest_ApiAuthenticationRecord::model()->findByPk($id);

        if($record)
        {
            return Rest_ApiAuthenticationModel::populateModel($record);
        }
    }

    /**
     * Save ApiAuthentication Token
     */
    public function saveApiAuthenticationToken($apiHandle, $token)
    {
        $api = craft()->rest_apis->getApi($apiHandle);
        $oauthProviderHandle = $api->getOAuthProviderHandle();

        craft()->rest->checkRequirements();

        // get apiAuthentication

        $apiAuthentication = $this->getApiAuthenticationByHandle($apiHandle);

        if(!$apiAuthentication)
        {
            $apiAuthentication = new Rest_ApiAuthenticationModel;
        }


        // save token

        $token->id = $apiAuthentication->tokenId;
        $token->providerHandle = $oauthProviderHandle;
        $token->pluginHandle = 'rest';

        craft()->oauth->saveToken($token);


        // save apiAuthentication

        $apiAuthentication->apiHandle = $apiHandle;
        $apiAuthentication->tokenId = $token->id;

        $this->saveApiAuthentication($apiAuthentication);
    }

    /**
     * Delete ApiAuthentication By ID
     */
    public function deleteApiAuthenticationById($id)
    {
        craft()->rest->checkRequirements();

        $apiAuthentication = $this->getApiAuthenticationById($id);


        // delete token

        if($apiAuthentication->tokenId)
        {
            $token = craft()->oauth->getTokenById($apiAuthentication->tokenId);

            if($token)
            {
                craft()->oauth->deleteToken($token);
            }
        }

        return Rest_ApiAuthenticationRecord::model()->deleteByPk($id);
    }

    /**
     * Get ApiAuthentications
     */
    public function getApiAuthentications()
    {
        $records = Rest_ApiAuthenticationRecord::model()->findAll(array('order' => 't.id'));
        return Rest_ApiAuthenticationModel::populateModels($records, 'id');
    }

    /**
     * Save ApiAuthentication
     */
    public function saveApiAuthentication(Rest_ApiAuthenticationModel $model)
    {
        $record = $this->_getApiAuthenticationRecordByHandle($model->apiHandle);

        if(!$record)
        {
            $record = new Rest_ApiAuthenticationRecord;
        }

        $record->apiHandle = $model->apiHandle;
        $record->tokenId = $model->tokenId;
        $record->scopes = $model->scopes;

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
}