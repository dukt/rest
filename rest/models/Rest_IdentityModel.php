<?php
namespace Craft;

class Rest_IdentityModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'id'    => AttributeType::Number,
            'tokenId' => AttributeType::Number,
            'name' => AttributeType::String,
            'handle' => AttributeType::String,
            'provider' => AttributeType::String,
            'scopes' => AttributeType::Mixed,
            'params' => AttributeType::Mixed,
        );
    }

    public function getToken()
    {
        return craft()->oauth->getTokenById($this->tokenId);
    }
}