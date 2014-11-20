<?php

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