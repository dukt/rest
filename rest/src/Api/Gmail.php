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

class Gmail extends AbstractApi {

    public function getName()
    {
        return "Gmail";
    }

    public function getProviderHandle()
    {
        return 'google';
    }

    public function getApiUrl()
    {
        return 'https://www.googleapis.com/gmail/v1/';
    }

    public function getScopes()
    {
        return array(
            'https://mail.google.com/mail/feed/atom',
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