<?php
/**
 * @link      https://dukt.net/craft/rest/
 * @copyright Copyright (c) 2016, Dukt
 * @license   https://dukt.net/craft/rest/docs#license
 */

namespace Craft;

class RestController extends BaseController
{
    public function init()
    {
        $plugin = craft()->plugins->getPlugin('rest');

        $variables['pluginDependencies'] = $plugin->getPluginDependencies();

        if(count($variables['pluginDependencies']) > 0)
        {
            $this->renderTemplate('rest/_dependencies', $variables);
        }
    }
}