<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
 */

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
            'url' => array(AttributeType::String, 'required' => true),
            'authenticationHandle' => AttributeType::String,
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