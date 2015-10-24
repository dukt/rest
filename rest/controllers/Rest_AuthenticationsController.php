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

class Rest_AuthenticationsController extends BaseController
{
    public function actionIndex()
    {
        $providers = craft()->oauth->getProviders();
        $authentications = craft()->rest->getAuthentications();

        $variables['providers'] = $providers;
        $variables['authentications'] = $authentications;

        $this->renderTemplate('rest/authentications/_index', $variables);
    }
}