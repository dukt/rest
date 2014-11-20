<?php

namespace Craft;

class Rest_RequestModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'id'    => AttributeType::Number,
            'api' => AttributeType::Number,
            'name' => AttributeType::String,
            'handle' => AttributeType::String,
            'verb' => AttributeType::String,
            'format' => AttributeType::String,
            'url' => AttributeType::String,
            'headers' => AttributeType::Mixed,
            'query' => AttributeType::Mixed,
        );
    }

    public function getAuthentication()
    {
        return craft()->rest->getAuthenticationByHandle($this->api);
    }
}