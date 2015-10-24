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

    public function actionDisconnect()
    {
        $handle = craft()->request->getParam('handle');

        $authentication = craft()->rest_authentications->getAuthenticationByHandle($handle);

        if($authentication)
        {
            craft()->rest_authentications->deleteAuthenticationById($authentication->id);
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
            $redirect = 'rest/authentications';
        }

        $oauthProvider = craft()->oauth->getProvider($handle);

        if($oauthProvider)
        {
            if($response = craft()->oauth->connect(array(
                'plugin' => 'rest',
                'provider' => $oauthProvider->getHandle(),
                'scopes' => $oauthProvider->getScopes(),
                'params' => $oauthProvider->getParams(),
            )))
            {
                if($response['success'])
                {
                    // save token
                    craft()->rest_authentications->saveAuthenticationToken($handle, $response['token']);

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
}