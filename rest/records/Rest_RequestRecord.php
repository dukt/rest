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
            'headers' => AttributeType::Mixed,
            'query' => AttributeType::Mixed,
        );
    }

    public function defineRelations()
    {
        return array(
            'identity' => array(static::BELONGS_TO, 'Rest_IdentityRecord', 'identityId', 'required' => false, 'onDelete' => static::SET_NULL)
        );
    }

    public function defineIndexes()
    {
        return array(
            array('columns' => array('handle'), 'unique' => true),
        );
    }
}