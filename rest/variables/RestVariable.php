<?php

namespace Craft;

class RestVariable {

    public function getIdentities()
    {
        return craft()->rest->getIdentities();
    }

    public function getRequests()
    {
        return craft()->rest->getRequests();
    }

    public function get($requestHandle, $queryParams)
    {
        return craft()->rest->get($requestHandle, $queryParams);
    }

    public function request($options)
    {
        return craft()->rest->request($options);
    }

    public function api($url, $headers = array(), $enableCache = false, $cacheDuration = 0)
    {
        return craft()->rest->api($url, $headers, $enableCache, $cacheDuration);
    }
}

