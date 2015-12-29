<?php

/**
* Craft REST by Dukt
 *
 * @package   Craft REST
 * @author    Benjamin David
 * @copyright Copyright (c) 2015, Dukt
 * @link      https://dukt.net/craft/rest/
 * @license   https://dukt.net/craft/rest/docs#license
 */

namespace Craft;

class Rest_RequestCriteriaModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'handle' => AttributeType::String,
            'url' => AttributeType::String,
            'uri' => AttributeType::String,
            'method' => AttributeType::String,
            'headers' => AttributeType::Mixed,
            'query' => AttributeType::Mixed,
            'authentication' => AttributeType::String,
            'token' => 'Oauth_TokenModel',
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