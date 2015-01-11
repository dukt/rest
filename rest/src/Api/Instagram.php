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

class Instagram extends AbstractApi {

    public function getName()
    {
        return "Instagram";
    }

    public function getProviderHandle()
    {
        return 'instagram';
    }

    public function getApiUrl()
    {
        return 'https://api.instagram.com/v1/'; // media/popular
    }

    public function getScopes()
    {
        return array(
            'basic',
            'comments',
            'relationships',
            'likes',
        );
    }
}