<?php

/**
* Craft REST by Dukt
 *
 * @package   Craft REST
 * @author    Benjamin David
 * @copyright Copyright (c) 2014, Dukt
 * @link      https://dukt.net/craft/rest/
 * @license   https://dukt.net/craft/rest/docs#license
 */

namespace Craft;

class RestPlugin extends BasePlugin
{
    /**
     * Get Name
     */
    function getName()
    {
        return Craft::t('REST');
    }

    /**
     * Get Version
     */
    function getVersion()
    {
        return '0.9.1';
    }

    /**
     * Get Developer
     */
    function getDeveloper()
    {
        return 'Dukt';
    }

    /**
     * Get Developer URL
     */
    function getDeveloperUrl()
    {
        return 'http://dukt.net/';
    }

    /**
     * Has CP Section
     */
    public function hasCpSection()
    {
        return true;
    }

    /**
     * Register CP Routes
     */
    public function registerCpRoutes()
    {
        return array(
            "rest" => array('action' => "rest/requestsIndex"),
            "rest/requests" => array('action' => "rest/requestsIndex"),
            "rest/requests/(?P<requestId>\d+)" => array('action' => "rest/editRequest"),
            "rest/requests/new" => array('action' => "rest/editRequest"),
            "rest/apis" => array('action' => "rest/apisIndex"),
        );
    }

    /**
     * On Before Uninstall
     */
    public function onBeforeUninstall()
    {
        if(isset(craft()->oauth))
        {
            craft()->oauth->deleteTokensByPlugin('rest');
        }
    }
}
