<?php

namespace Craft;

class RestVariable {

    public function getIdentities()
    {
        return craft()->rest->getIdentities();
    }

    public function get($url, $headers = array(), $enableCache = false, $cacheDuration = 0)
    {
        return craft()->rest->get($url, $headers, $enableCache, $cacheDuration);
    }
}

