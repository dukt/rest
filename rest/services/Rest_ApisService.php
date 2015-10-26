<?php

/**
* Craft REST by Dukt
 *
 * @package   Craft REST
 * @author    Benjamin David
 * @copyright Copyright (c) 2015, Dukt
 * @link      https://dukt.net/craft/rest/
 * @license   https://dukt.net/craft/rest/docs#license
 */

namespace Craft;

class Rest_ApisService extends BaseApplicationComponent
{
    private $apis;

    public function getApis()
    {
        if(!$this->apis)
        {
            $this->apis = $this->_getApis();
        }

        return $this->apis;
    }

    private function _getApis()
    {
        // fetch classes

        $apiClasses = array();

        foreach(craft()->plugins->call('getApis', [], true) as $pluginApiClasses)
        {
            $apiClasses = array_merge($apiClasses, $pluginApiClasses);
        }


        // instances

        $apiInstances = [];

        foreach($apiClasses as $apiClass)
        {
            $apiInstances[$apiClass] = $this->_createApi($apiClass);
        }

        ksort($apiInstances);

        return $apiInstances;
    }

    private function _createApi($apiClass)
    {
        $api = new $apiClass;

        return $api;
    }

    public function getApi($handle)
    {
        foreach($this->getApis() as $api)
        {
            if($api->getHandle() == $handle)
            {
                return $api;
            }
        }
    }
}