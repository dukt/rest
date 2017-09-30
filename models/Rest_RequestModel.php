<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
 */

namespace Craft;

class Rest_RequestModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'id'    => AttributeType::Number,
            'name' => AttributeType::String,
            'handle' => AttributeType::String,

            'authenticationHandle' => AttributeType::String,

            'url' => AttributeType::String,
            'headers' => AttributeType::Mixed,
            'query' => AttributeType::Mixed,
        );
    }

    public function getAuthentication()
    {
        return craft()->rest_authentications->getAuthenticationByHandle($this->authenticationHandle);
    }
}