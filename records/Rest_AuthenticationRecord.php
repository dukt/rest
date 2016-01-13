<?php
/**
 * @link      https://dukt.net/craft/rest/
 * @copyright Copyright (c) 2016, Dukt
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
            'authenticationHandle' => array(AttributeType::String, 'required' => true),
        );
    }

    public function defineIndexes()
    {
        return array(
            array('columns' => array('authenticationHandle'), 'unique' => true),
        );
    }
}