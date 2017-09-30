<?php
/**
 * @link      https://dukt.net/craft/rest/
 * @copyright Copyright (c) 2017, Dukt
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
        return craft()->oauth->getProvider($this->authenticationHandle);
    }

    public function getToken()
    {
        return craft()->oauth->getTokenById($this->tokenId);
    }
}
