<?php
/**
 * @link      https://dukt.net/craft/rest/
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://dukt.net/craft/rest/docs#license
 */

namespace Craft;

class RestVariable {

    public function request($attributes = null)
    {
        return craft()->rest->request($attributes);
    }

    public function getAuthenticationByHandle($handle)
    {
        return craft()->rest_authentications->getAuthenticationByHandle($handle);
    }
}