<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
 */

namespace Craft;

class Rest_AuthenticationsService extends BaseApplicationComponent
{
    public function getAuthenticationProvider($handle)
    {
        $authenticationProviders = $this->getAuthenticationProviders();

        if(isset($authenticationProviders[$handle]))
        {
            return $authenticationProviders[$handle];
        }
    }

    public function getAuthenticationProviders($enabledOnly = true)
    {
        $authenticationProviders = [];

        $oauthProviders = craft()->oauth->getProviders($enabledOnly);

        foreach($oauthProviders as $oauthProvider)
        {
            $authenticationProvider = new Rest_AuthenticationProviderModel;
            $authenticationProvider->oauthProviderHandle = $oauthProvider->getHandle();

            $authenticationProviders[$authenticationProvider->oauthProviderHandle] = $authenticationProvider;
        }

        return $authenticationProviders;
    }

    /**
     * Get Authentication By Handle
     */
    public function getAuthenticationByHandle($authenticationHandle)
    {
        $record = Rest_AuthenticationRecord::model()->find(
            array(
                'condition' => 'authenticationHandle=:authenticationHandle',
                'params' => array(':authenticationHandle' => $authenticationHandle)
            )
        );

        if($record)
        {
            return Rest_AuthenticationModel::populateModel($record);
        }
    }

    /**
     * Get Authentication By Handle
     */
    public function _getAuthenticationRecordByHandle($authenticationHandle)
    {
        return Rest_AuthenticationRecord::model()->find(
            array(
                'condition' => 'authenticationHandle=:authenticationHandle',
                'params' => array(':authenticationHandle' => $authenticationHandle)
            )
        );
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
    public function saveAuthenticationToken($providerHandle, $token)
    {
        // get authentication

        $authentication = $this->getAuthenticationByHandle($providerHandle);

        if(!$authentication)
        {
            $authentication = new Rest_AuthenticationModel;
        }


        // save token

        $tokenExists = craft()->oauth->getTokenById($authentication->tokenId);

        if($tokenExists)
        {
            $token->id = $authentication->tokenId;
        }

        $token->providerHandle = $providerHandle;
        $token->pluginHandle = 'rest';

        craft()->oauth->saveToken($token);


        // save authentication

        $authentication->authenticationHandle = $providerHandle;
        $authentication->tokenId = $token->id;

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
        $record = $this->_getAuthenticationRecordByHandle($model->authenticationHandle);

        if(!$record)
        {
            $record = new Rest_AuthenticationRecord;
        }

        $record->authenticationHandle = $model->authenticationHandle;
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
}
