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

class Vimeo extends AbstractApi {

    public function getName()
    {
        return "Vimeo";
    }

    public function getProviderHandle()
    {
        return 'vimeo';
    }

    public function getApiUrl()
    {
        return 'https://api.vimeo.com';
    }
}