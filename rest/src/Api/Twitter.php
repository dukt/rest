<?php

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