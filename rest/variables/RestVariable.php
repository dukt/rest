<?php

namespace Craft;

class RestVariable {

    public function request($attributes = null)
    {
        return craft()->rest->request($attributes);
    }

    public function getAuthenticationByHandle($handle)
    {
        return craft()->rest->getAuthenticationByHandle($handle);
    }
}