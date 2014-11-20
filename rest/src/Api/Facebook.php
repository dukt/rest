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

class Facebook extends AbstractApi {

    public function getName()
    {
        return "Facebook";
    }

    public function getProviderHandle()
    {
        return 'facebook';
    }

    public function getApiUrl()
    {
        return 'https://graph.facebook.com/';
    }
}