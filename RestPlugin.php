<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
 */

namespace Craft;

class RestPlugin extends BasePlugin
{
    /**
     * Get Name
     */
    public function getName()
    {
        return Craft::t('REST');
    }

    /**
     * Get Version
     */
    public function getVersion()
    {
        return '1.1.1';
    }

    /**
     * Get Schema Version
     *
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * Get Required Dependencies
     */
    public function getRequiredPlugins()
    {
        return array(
            array(
                'name' => "OAuth",
                'handle' => 'oauth',
                'url' => 'https://github.com/dukt/oauth',
                'version' => '1.0.0'
            )
        );
    }

    /**
     * Get Developer
     */
    public function getDeveloper()
    {
        return 'Dukt';
    }

    /**
     * Get Developer URL
     */
    public function getDeveloperUrl()
    {
        return 'http://dukt.net/';
    }

    /**
     * Get Documentation URL
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/dukt/restdocs/';
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
            "rest" => array('action' => "rest/requests/index"),
            "rest/install" => array('action' => "rest/install/index"),
            "rest/requests" => array('action' => "rest/requests/index"),
            "rest/requests/(?P<requestId>\d+)" => array('action' => "rest/requests/edit"),
            "rest/requests/new" => array('action' => "rest/requests/edit"),
            "rest/authentications" => array('action' => "rest/authentications/index"),
            "rest/authentications/(?P<authenticationProviderHandle>{handle})" => array('action' => "rest/authentications/edit"),
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

    /* ------------------------------------------------------------------------- */

    /**
     * Get Plugin Dependencies
     */
    public function getPluginDependencies($missingOnly = true)
    {
        $dependencies = array();

        $plugins = $this->getRequiredPlugins();

        foreach($plugins as $key => $plugin)
        {
            $dependency = $this->getPluginDependency($plugin);

            if($missingOnly)
            {
                if($dependency['isMissing'])
                {
                    $dependencies[] = $dependency;
                }
            }
            else
            {
                $dependencies[] = $dependency;
            }
        }

        return $dependencies;
    }

    /**
     * Get Plugin Dependency
     */
    private function getPluginDependency($dependency)
    {
        $isMissing = true;
        $isInstalled = true;

        $plugin = craft()->plugins->getPlugin($dependency['handle'], false);

        if($plugin)
        {
            $currentVersion = $plugin->version;


            // requires update ?

            if(version_compare($currentVersion, $dependency['version']) >= 0)
            {
                // no (requirements OK)

                if($plugin->isInstalled && $plugin->isEnabled)
                {
                    $isMissing = false;
                }
            }
            else
            {
                // yes (requirement not OK)
            }
        }
        else
        {
            // not installed
        }

        $dependency['isMissing'] = $isMissing;
        $dependency['plugin'] = $plugin;

        return $dependency;
    }

    /**
     * Get release feed URL
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/dukt/rest/v1/releases.json';
    }
}
