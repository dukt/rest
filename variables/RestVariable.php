<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
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