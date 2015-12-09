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
        $authenticationProviders = craft()->rest_authentications->getAuthenticationProviders(false);
        $authentications = craft()->rest_authentications->getAuthentications();

        $variables['authenticationProviders'] = $authenticationProviders;
        $variables['authentications'] = $authentications;

        $this->renderTemplate('rest/authentications/_index', $variables);
    }

    public function actionEdit(array $variables = array())
    {
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