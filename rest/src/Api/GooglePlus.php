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

namespace Dukt\Rest\Api;

class GooglePlus extends AbstractApi {

    public function getName()
    {
        return "Google+";
    }

    public function getProviderHandle()
    {
        return 'google';
    }

    public function getApiUrl()
    {
        return 'https://www.googleapis.com/plus/v1/';
    }

    public function getScopes()
    {
        return array(
            'https://www.googleapis.com/auth/plus.login',
            'https://www.googleapis.com/auth/plus.me',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        );
    }

    public function getParams()
    {
        return array(
            'approval_prompt' => 'force',
            'access_type' => 'offline',
        );
    }
}



