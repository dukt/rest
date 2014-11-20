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

class RunKeeper extends AbstractApi {

    public function getName()
    {
        return "RunKeeper";
    }

    public function getProviderHandle()
    {
        return 'runkeeper';
    }

    public function getApiUrl()
    {
        return 'https://api.runkeeper.com/';
    }
}