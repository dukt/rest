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

class Rest_ApisController extends BaseController
{
    public function actionIndex()
    {
        $apis = craft()->rest_apis->getApis();

        $variables['apis'] = $apis;

        $this->renderTemplate('rest/apis/_index', $variables);
    }
}