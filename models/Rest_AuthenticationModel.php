<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
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
