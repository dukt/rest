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

class Rest_AuthenticationModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'id'    => AttributeType::Number,
            'tokenId' => AttributeType::Number,
            'authenticationHandle' => AttributeType::String,
        );
    }

    public function getOauthProvider()
    {
        $oauthProvider = craft()->oauth->getProvider($this->authenticationHandle);
        return $oauthProvider;
    }

    public function getToken()
    {
        craft()->rest->checkRequirements();

        return craft()->oauth->getTokenById($this->tokenId);
    }
}