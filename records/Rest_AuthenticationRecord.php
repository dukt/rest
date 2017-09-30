<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
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