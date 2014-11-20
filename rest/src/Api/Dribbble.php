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

class Dribbble extends AbstractApi {

    public function getName()
    {
        return "Dribbble";
    }

    public function getProviderHandle()
    {
        return 'dribbble';
    }

    public function getApiUrl()
    {
        return 'https://api.dribbble.com/v1/';
    }
}