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
            'apiHandle' => array(AttributeType::String, 'required' => true),
        );
    }

    public function defineIndexes()
    {
        return array(
            array('columns' => array('apiHandle'), 'unique' => true),
        );
    }
}