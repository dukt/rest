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
        $authenticationProviders = craft()->rest_authentications->getAuthenticationProviders();
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

    public function actionSave()
    {
        $connect = craft()->request->getPost('connect');

        $authenticationProviderHandle = craft()->request->getRequiredPost('authenticationProviderHandle');

        $scopes = craft()->request->getPost('scopes');

        $customScopes = [];

        $postCustomScopes = craft()->request->getPost('customScopes');

        if($postCustomScopes)
        {
            foreach($postCustomScopes as $postCustomScope)
            {
                $customScopes[] = $postCustomScope['scope'];
            }
        }

        $customAuthorizationOptions = [];

        $postCustomAuthorizationOptions = craft()->request->getPost('customAuthorizationOptions');

        if($postCustomAuthorizationOptions)
        {
            foreach($postCustomAuthorizationOptions as $postCustomAuthorizationOption)
            {
                $customAuthorizationOptions[$postCustomAuthorizationOption['authorizationOptionKey']] = $postCustomAuthorizationOption['authorizationOptionValue'];
            }
        }

        $authentication = new Rest_AuthenticationModel;
        $authentication->authenticationHandle = $authenticationProviderHandle;
        $authentication->scopes = $scopes;
        $authentication->customScopes = $customScopes;
        $authentication->customAuthorizationOptions = $customAuthorizationOptions;

        if(craft()->rest_authentications->saveAuthentication($authentication))
        {
            craft()->userSession->setNotice(Craft::t('Authentication saved.'));

            if($connect)
            {
                $redirectUrl = UrlHelper::getActionUrl('rest/authentications/connect', ['handle' => $authenticationProviderHandle]);

                $this->redirect($redirectUrl);
            }
            else
            {
                $this->redirectToPostedUrl();
            }
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

    public function actionConnect()
    {
        $authenticationProviderHandle = craft()->request->getParam('handle');
        $redirect = craft()->request->getParam('redirect');

        if(!$redirect)
        {
            $redirect = 'rest/authentications';
        }

        $authenticationProvider = craft()->rest_authentications->getAuthenticationProvider($authenticationProviderHandle);

        $oauthProviderHandle = $authenticationProvider['oauthProviderHandle'];

        $oauthProvider = craft()->oauth->getProvider($oauthProviderHandle);

        $authentication = craft()->rest_authentications->getAuthenticationByHandle($authenticationProviderHandle);

        if($oauthProvider)
        {
            if($response = craft()->oauth->connect(array(
                'plugin' => 'rest',
                'provider' => $oauthProvider->getHandle(),
                'scope' => $authentication->getAllScopes(),
                'authorizationOptions' => $authentication->getAllAuthorizationOptions(),
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
        }
        else
        {
            craft()->userSession->setError(Craft::t("OAuth provider not configured."));
        }

        $this->redirect($redirect);
    }
}