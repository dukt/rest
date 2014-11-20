<?php

namespace Craft;

class Rest_RequestRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'rest_requests';
    }

    protected function defineAttributes()
    {
        return array(
            'name' => array(AttributeType::String, 'required' => true),
            'handle' => array(AttributeType::String, 'required' => true),
            'verb' => array(AttributeType::String, 'required' => true),
            'format' => array(AttributeType::String, 'required' => true),
            'url' => array(AttributeType::String, 'required' => true),
            'api' => AttributeType::String,
            'headers' => AttributeType::Mixed,
            'query' => AttributeType::Mixed,
        );
    }

    public function defineIndexes()
    {
        return array(
            array('columns' => array('handle'), 'unique' => true),
        );
    }
}