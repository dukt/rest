<?php
namespace Craft;

class Rest_RequestModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'id'    => AttributeType::Number,
            'identityId' => AttributeType::Number,
            'name' => AttributeType::String,
            'handle' => AttributeType::String,
            'verb' => AttributeType::String,
            'format' => AttributeType::String,
            'url' => AttributeType::String,
            'params' => AttributeType::Mixed,
        );
    }

    public function getIdentity()
    {
        return craft()->rest->getIdentityById($this->identityId);
    }
}