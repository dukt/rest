<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
 */

namespace Craft;

class Rest_OauthController extends BaseController
{
    public function actionConnect()
    {
        $authenticationProviderHandle = craft()->request->getParam('handle');

        // referer

        $referer = craft()->httpSession->get('rest.referer');

        if (!$referer)
        {
            $referer = craft()->request->getUrlReferrer();

            craft()->httpSession->add('rest.referer', $referer);
        }


        $authenticationProvider = craft()->rest_authentications->getAuthenticationProvider($authenticationProviderHandle);

        $oauthProviderHandle = $authenticationProvider['oauthProviderHandle'];
        $oauthProvider = craft()->oauth->getProvider($oauthProviderHandle);

        if($oauthProvider)
        {
            if($response = craft()->oauth->connect(array(
                'plugin' => 'rest',
                'provider' => $oauthProvider->getHandle(),
                'scope' => $authenticationProvider->getScope(),
                'authorizationOptions' => $authenticationProvider->getAuthorizationOptions(),
            )))
            {
                if($response['success'])
                {
                    // save token
                    craft()->rest_authentications->saveAuthenticationToken($authenticationProviderHandle, $response['token']);

                    // session notice
                    craft()->userSession->setNotice(Craft::t("Connected."));
                }
                else
                {
                    craft()->userSession->setError(Craft::t($response['errorMsg']));
                }
            }
            else
            {
                craft()->userSession->setError(Craft::t("Couldn’t OAuth connect."));
            }
        }
        else
        {
            craft()->userSession->setError(Craft::t("OAuth provider not configured."));
        }

        craft()->httpSession->remove('rest.referer');

        $this->redirect($referer);
    }
}