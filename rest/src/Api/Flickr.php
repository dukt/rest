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

class Flickr extends AbstractApi {

    public function getName()
    {
        return "Flickr";
    }

    public function getProviderHandle()
    {
        return 'flickr';
    }

    public function getApiUrl()
    {
        return 'https://api.flickr.com/services/rest/'; // ?method=flickr.photos.getRecent
    }
}