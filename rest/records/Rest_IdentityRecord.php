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
            'provider' => array(AttributeType::String, 'required' => true),
            'scopes' => array(AttributeType::Mixed),
            'params' => array(AttributeType::Mixed),
        );
    }

}