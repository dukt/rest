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

class Rest_ApiAuthenticationsController extends BaseController
{

    public function actionSave()
    {
        $apiHandle = craft()->request->getRequiredPost('apiHandle');
        $scopes = craft()->request->getPost('scopes');

        $apiAuthentication = new Rest_ApiAuthenticationModel;
        $apiAuthentication->apiHandle = $apiHandle;
        $apiAuthentication->scopes = $scopes;

        if(craft()->rest_apiAuthentications->saveApiAuthentication($apiAuthentication))
        {
            craft()->userSession->setNotice(Craft::t('ApiAuthentication saved.'));
            // $this->redirectToPostedUrl();

            $redirectUrl = UrlHelper::getActionUrl('rest/apis/connect', ['handle' => $apiHandle]);

            $this->redirect($redirectUrl);
        }
        else
        {
            craft()->userSession->setError(Craft::t('Couldn’t save apiAuthentication.'));

            // Send the request back to the template
            craft()->urlManager->setRouteVariables(array(
                'apiAuthentication' => $apiAuthentication
            ));
        }
    }
}