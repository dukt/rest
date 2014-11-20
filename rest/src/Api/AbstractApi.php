<?php

namespace Dukt\Rest\Api;

abstract class AbstractApi {

    public function getHandle()
    {
        // from : \Dukt\Rest\Api\YouTube
        // to : youtube

        $handle = get_class($this);

        $start = strlen("\\Dukt\\Rest\\Api\\") - 1;

        $handle = substr($handle, $start);

        $handle = strtolower($handle);

        return $handle;
    }

    public function getApiUrl()
    {
        return null;
    }

    public function getScopes()
    {
        return null;
    }

    public function getParams()
    {
        return null;
    }
}
