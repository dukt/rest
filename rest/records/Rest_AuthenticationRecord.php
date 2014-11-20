<?php

namespace Craft;

class Rest_AuthenticationRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'rest_authentications';
    }

    protected function defineAttributes()
    {
        return array(
            'tokenId' => AttributeType::Number,
            'handle' => array(AttributeType::String, 'required' => true),
        );
    }

    public function defineIndexes()
    {
        return array(
            array('columns' => array('handle'), 'unique' => true),
        );
    }
}