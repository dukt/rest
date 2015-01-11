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

class GoogleAnalytics extends AbstractApi {

    public function getName()
    {
        return "Google Analytics";
    }

    public function getProviderHandle()
    {
        return 'google';
    }

    public function getApiUrl()
    {
        return 'https://www.googleapis.com/analytics/v3/';
    }

    public function getScopes()
    {
        return array(
            'https://www.googleapis.com/auth/analytics',
            'https://www.googleapis.com/auth/analytics.edit',
            'https://www.googleapis.com/auth/analytics.manage.users',
            'https://www.googleapis.com/auth/analytics.readonly',
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