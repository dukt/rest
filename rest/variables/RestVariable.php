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

class RestVariable {

    public function request($attributes = null)
    {
        return craft()->rest->request($attributes);
    }

    public function getAuthenticationByHandle($handle)
    {
        return craft()->rest->getAuthenticationByHandle($handle);
    }
}