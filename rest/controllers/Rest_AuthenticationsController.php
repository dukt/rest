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
        $authentications = craft()->rest_authentications->getAuthentications();

        $variables['providers'] = $providers;
        $variables['authentications'] = $authentications;

        $this->renderTemplate('rest/authentications/_index', $variables);
    }

    public function actionEdit(array $variables = array())
    {
        $providerHandle = $variables['providerHandle'];

        $variables['provider'] = craft()->oauth->getProvider($providerHandle);
        $variables['authentication'] = craft()->rest_authentications->getAuthenticationByHandle($providerHandle);

        $this->renderTemplate('rest/authentications/_edit', $variables);
    }

    public function actionSaveAuthentication()
    {
        $authenticationHandle = craft()->request->getRequiredPost('authenticationHandle');
        $scopes = craft()->request->getPost('scopes');

        $authentication = new Rest_AuthenticationModel;
        $authentication->authenticationHandle = $authenticationHandle;
        $authentication->scopes = $scopes;

        craft()->rest_authentications->saveAuthentication($authentication);
    }
}