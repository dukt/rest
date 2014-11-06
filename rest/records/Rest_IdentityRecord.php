<?php

namespace Craft;

class Rest_IdentityRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'rest_identities';
    }

    protected function defineAttributes()
    {
        return array(
            'tokenId' => AttributeType::Number,
            'name' => array(AttributeType::String, 'required' => true),
            'handle' => array(AttributeType::String, 'required' => true),
            'providerHandle' => array(AttributeType::String, 'required' => true),
            'scopes' => array(AttributeType::Mixed),
            'params' => array(AttributeType::Mixed),
        );
    }

}