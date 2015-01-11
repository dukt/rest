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

class Twitter extends AbstractApi {

    public function getName()
    {
        return "Twitter";
    }

    public function getProviderHandle()
    {
        return 'twitter';
    }

    public function getApiUrl()
    {
        return 'https://api.twitter.com/1.1/';
    }
}