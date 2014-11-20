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

class BitBucket extends AbstractApi {

    public function getName()
    {
        return "BitBucket";
    }

    public function getProviderHandle()
    {
        return 'bitbucket';
    }

    public function getApiUrl()
    {
        return 'https://api.bitbucket.org/2.0/'; // repositories
    }
}