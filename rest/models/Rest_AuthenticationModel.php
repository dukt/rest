<?php

namespace Craft;

class Rest_AuthenticationModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'id'    => AttributeType::Number,
            'tokenId' => AttributeType::Number,
            'handle' => AttributeType::String,
        );
    }

    public function getToken()
    {
        return craft()->oauth->getTokenById($this->tokenId);
    }
}