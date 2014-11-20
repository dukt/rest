<?php

/**
* Craft REST by Dukt
 *
 * @package   Craft REST
 * @author    Benjamin David
 * @copyright Copyright (c) 2014, Dukt
 * @link      https://dukt.net/craft/rest/
 * @license   https://dukt.net/craft/rest/docs#license
 */

namespace Craft;

class Rest_RequestModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'id'    => AttributeType::Number,
            'apiHandle' => AttributeType::Number,
            'name' => AttributeType::String,
            'handle' => AttributeType::String,
            'verb' => AttributeType::String,
            'format' => AttributeType::String,
            'url' => AttributeType::String,
            'headers' => AttributeType::Mixed,
            'query' => AttributeType::Mixed,
        );
    }
}