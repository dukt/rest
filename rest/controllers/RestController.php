<?php

namespace Craft;

class RestController extends BaseController
{
    public function actionDeleteIdentity()
    {
       $this->requirePostRequest();
       $this->requireAjaxRequest();

       $id = craft()->request->getRequiredPost('id');

       craft()->rest->deleteIdentityById($id);

       $this->returnJson(array('success' => true));
    }

    public function actionSaveIdentity()
    {
        $id = craft()->request->getParam('id');
        $provider = craft()->request->getParam('provider');
        $scopes = craft()->request->getParam('scopes');
        $params = craft()->request->getParam('params');
        $redirect = craft()->request->getParam('redirect');

        if($scopes)
        {
            $newScopes = array();

            foreach($scopes as $scope)
            {
                $newScopes[] = $scope['scope'];
            }

            $scopes = $newScopes;
        }
        else
        {
            $scopes = array();
        }

        if($params)
        {
            $newParams = array();

            foreach($params as $param)
            {
                $newParams[$param['key']] = $param['value'];
            }

            $params = $newParams;
        }
        else
        {
            $params = array();
        }

        if($id)
        {
            $identity = craft()->rest->getIdentityById($id);
        }

        if(!isset($identity))
        {
            $identity = new Rest_IdentityModel;
        }

        $identity->id = $id;
        $identity->provider = $provider;
        $identity->scopes = $scopes;
        $identity->params = $params;

        craft()->rest->saveIdentity($identity);

        $this->redirect(UrlHelper::getActionUrl('rest/connect', array(
                'identityId' => $id,
                'redirect' => $redirect
        )));
    }

    public function actionConnect()
    {
        $identityId = craft()->request->getParam('identityId');
        $redirect = craft()->request->getParam('redirect');

        $identity = craft()->rest->getIdentityById($identityId);

        if($identity)
        {
            if($response = craft()->oauth->connect(array(
                'plugin' => 'rest',
                'provider' => $identity->provider,
                'scopes' => $identity->scopes,
                'params' => $identity->params,
            )))
            {
                if($response['success'])
                {
                    // token
                    $token = $response['token'];

                    // save token
                    craft()->rest->saveToken($identity, $token);

                    // session notice
                    craft()->userSession->setNotice(Craft::t("Connected."));
                }
                else
                {
                    craft()->userSession->setError(Craft::t($response['errorMsg']));
                }
            }
        }

        $this->redirect($redirect);
    }

    public function actionDisconnect()
    {
        $id = craft()->request->getParam('identity');

        $identity = craft()->rest->getIdentityById($id);

        if($identity)
        {
            craft()->rest->saveToken($identity, null);
        }

        $referrer = craft()->request->getUrlReferrer();

        $this->redirect($referrer);
    }

    public function actionEditIdentity(array $variables = array())
    {
        if (!empty($variables['id']))
        {
            $variables['isNew'] = false;
            $variables['identity'] = craft()->rest->getIdentityById($variables['id']);
        }
        else
        {
            $variables['isNew'] = true;
            $variables['identity'] = new Rest_IdentityModel;
        }

        $this->renderTemplate('rest/identities/_edit', $variables);
    }
    public function actionEditRequest()
    {
        $this->renderTemplate('rest/requests/_edit');
    }
}