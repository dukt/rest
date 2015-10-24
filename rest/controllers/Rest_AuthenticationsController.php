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

    public function actionSave()
    {
        $authenticationHandle = craft()->request->getRequiredPost('authenticationHandle');
        $scopes = craft()->request->getPost('scopes');

        $authentication = new Rest_AuthenticationModel;
        $authentication->oauthProviderHandle = $authenticationHandle;
        $authentication->scopes = $scopes;

        if(craft()->rest_authentications->saveAuthentication($authentication))
        {
            craft()->userSession->setNotice(Craft::t('Authentication saved.'));
            // $this->redirectToPostedUrl();

            $redirectUrl = UrlHelper::getActionUrl('rest/connect', ['handle' => $authenticationHandle]);

            $this->redirect($redirectUrl);
        }
        else
        {
            craft()->userSession->setError(Craft::t('Couldn’t save authentication.'));

            // Send the request back to the template
            craft()->urlManager->setRouteVariables(array(
                'authentication' => $authentication
            ));
        }
    }
}