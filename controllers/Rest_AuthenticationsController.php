<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
 */

namespace Craft;

class Rest_AuthenticationsController extends BaseController
{
    public function actionIndex()
    {
        $variables['checkDependencies'] = craft()->rest->checkDependencies();
        
        if($variables['checkDependencies'])
        {
            $variables['authenticationProviders'] = craft()->rest_authentications->getAuthenticationProviders(false);
            $variables['authentications'] = craft()->rest_authentications->getAuthentications();
        }

        $this->renderTemplate('rest/authentications/_index', $variables);
    }

    public function actionEdit(array $variables = array())
    {
        craft()->rest->requireDependencies();
        
        $authenticationProviderHandle = $variables['authenticationProviderHandle'];

        $authenticationProvider = craft()->rest_authentications->getAuthenticationProvider($authenticationProviderHandle);
        $authentication = craft()->rest_authentications->getAuthenticationByHandle($authenticationProviderHandle);

        $variables['authenticationProvider'] = $authenticationProvider;
        $variables['authentication'] = $authentication;

        $this->renderTemplate('rest/authentications/_edit', $variables);
    }

    public function actionConnect()
    {
        // referer

        $referer = craft()->httpSession->get('rest.referer');

        if (!$referer)
        {
            $referer = craft()->request->getUrlReferrer();

            craft()->httpSession->add('rest.referer', $referer);
        }


        // redirect to rest/oauth/connect

        $handle = craft()->request->getParam('handle');
        $redirectUrl = UrlHelper::getActionUrl('rest/oauth/connect', array('handle' => $handle));
        $this->redirect($redirectUrl);
    }

    public function actionDisconnect()
    {
        $handle = craft()->request->getParam('handle');

        $authentication = craft()->rest_authentications->getAuthenticationByHandle($handle);

        if($authentication)
        {
            craft()->oauth->deleteToken($authentication->getToken());

            $authentication->tokenId = null;

            craft()->rest_authentications->saveAuthentication($authentication);
        }

        craft()->userSession->setNotice(Craft::t("Disconnected."));

        $referrer = craft()->request->getUrlReferrer();

        $this->redirect($referrer);
    }
}
