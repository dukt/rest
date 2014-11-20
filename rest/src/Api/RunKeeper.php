<?php

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