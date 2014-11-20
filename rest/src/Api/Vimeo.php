<?php

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