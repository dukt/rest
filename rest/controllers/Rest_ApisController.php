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
        $variables['apiAuthentications'] = craft()->rest_apiAuthentications->getApiAuthentications();

        $this->renderTemplate('rest/apis/_index', $variables);
    }

    public function actionEdit(array $variables = array())
    {
        $apiHandle = $variables['apiHandle'];

        $variables['api'] = craft()->rest_apis->getApi($apiHandle);
        $variables['apiAuthentication'] = craft()->rest_apiAuthentications->getApiAuthenticationByHandle($apiHandle);

        $this->renderTemplate('rest/apis/_edit', $variables);
    }

    public function actionDisconnect()
    {
        $handle = craft()->request->getParam('handle');

        $apiAuthentication = craft()->rest_apiAuthentications->getApiAuthenticationByHandle($handle);

        if($apiAuthentication)
        {
            craft()->oauth->deleteToken($apiAuthentication->getToken());

            $apiAuthentication->tokenId = null;

            craft()->rest_apiAuthentications->saveApiAuthentication($apiAuthentication);
        }

        craft()->userSession->setNotice(Craft::t("Disconnected."));

        $referrer = craft()->request->getUrlReferrer();

        $this->redirect($referrer);
    }

    public function actionConnect()
    {
        $handle = craft()->request->getParam('handle');
        $redirect = craft()->request->getParam('redirect');

        if(!$redirect)
        {
            $redirect = 'rest/apis';
        }

        $api = craft()->rest_apis->getApi($handle);
        $oauthProviderHandle = $api->getOAuthProviderHandle();

        $oauthProvider = craft()->oauth->getProvider($oauthProviderHandle);
        $apiAuthentication = craft()->rest_apiAuthentications->getApiAuthenticationByHandle($handle);

        if($oauthProvider)
        {
            if($response = craft()->oauth->connect(array(
                'plugin' => 'rest',
                'provider' => $oauthProvider->getHandle(),
                'scopes' => $apiAuthentication->scopes,
                'params' => $oauthProvider->getParams(),
            )))
            {
                if($response['success'])
                {
                    // save token
                    craft()->rest_apiAuthentications->saveApiAuthenticationToken($handle, $response['token']);

                    // session notice
                    craft()->userSession->setNotice(Craft::t("Connected."));
                }
                else
                {
                    craft()->userSession->setError(Craft::t($response['errorMsg']));
                }
            }
        }
        else
        {
            craft()->userSession->setError(Craft::t("OAuth provider not configured."));
        }
        $this->redirect($redirect);
    }

    // public function actionIndex()
    // {
    //     $providers = craft()->oauth->getProviders();
    //     $apiAuthentications = craft()->rest_apiAuthentications->getApiAuthentications();

    //     $variables['providers'] = $providers;
    //     $variables['apiAuthentications'] = $apiAuthentications;

    //     $this->renderTemplate('rest/apiAuthentications/_index', $variables);
    // }

    // public function actionEdit(array $variables = array())
    // {
    //     $providerHandle = $variables['providerHandle'];

    //     $variables['provider'] = craft()->oauth->getProvider($providerHandle);
    //     $variables['apiAuthentication'] = craft()->rest_apiAuthentications->getApiAuthenticationByHandle($providerHandle);

    //     var_dump($apiAuthentication);

    //     $this->renderTemplate('rest/apiAuthentications/_edit', $variables);
    // }

}