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

namespace Dukt\Rest\Api;

abstract class AbstractApi {

    public function getHandle()
    {
        // from : \Dukt\Rest\Api\YouTube
        // to : youtube

        $handle = get_class($this);

        $start = strlen("\\Dukt\\Rest\\Api\\") - 1;

        $handle = substr($handle, $start);

        $handle = strtolower($handle);

        return $handle;
    }

    public function getDefaultQuery()
    {
        return array();
    }

    public function getApiUrl()
    {
        return null;
    }

    public function getScopes()
    {
        return array();
    }

    public function getParams()
    {
        return null;
    }

    public function getAuthorizationMethod()
    {
        return null;
    }
}
