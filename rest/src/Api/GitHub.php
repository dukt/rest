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

class GitHub extends AbstractApi {

    public function getName()
    {
        return "GitHub";
    }

    public function getProviderHandle()
    {
        return 'github';
    }

    public function getApiUrl()
    {
        return 'https://api.github.com/';
    }

    public function getScopes()
    {
        return array(
            'repo'
        );
    }
}