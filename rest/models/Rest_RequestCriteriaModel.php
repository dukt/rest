<?php

namespace Craft;

class Rest_RequestCriteriaModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'handle' => AttributeType::String,
            'url' => AttributeType::String,
            'uri' => AttributeType::String,
            'verb' => array(AttributeType::String, 'default' => 'get'),
            'format' => array(AttributeType::String, 'default' => 'json'),
            'headers' => AttributeType::Mixed,
            'query' => AttributeType::Mixed,
            'authentication' => AttributeType::String,
            'api' => AttributeType::String,
        );
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->send());
    }

    public function send()
    {
        return craft()->rest->sendRequest($this);
    }
}