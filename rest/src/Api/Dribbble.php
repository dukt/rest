<?php

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