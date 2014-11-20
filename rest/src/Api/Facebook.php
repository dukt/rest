<?php

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