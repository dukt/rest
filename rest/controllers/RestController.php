<?php

/**
* Craft REST by Dukt
 *
 * @package   Craft REST
 * @author    Benjamin David
 * @copyright Copyright (c) 2014, Dukt
 * @link      https://dukt.net/craft/rest/
 * @license   https://dukt.net/craft/rest/docs#license
 */

namespace Craft;

class RestController extends BaseController
{
    public function actionDisconnect()
    {
        $handle = craft()->request->getParam('handle');

        $authentication = craft()->rest->getAuthenticationByHandle($handle);

        if($authentication)
        {
            craft()->rest->deleteAuthenticationById($authentication->id);
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

        $api = craft()->rest->getApiByHandle($handle);

        if($api)
        {
            if($response = craft()->oauth->connect(array(
                'plugin' => 'rest',
                'provider' => $api->getProviderHandle(),
                'scopes' => $api->getScopes(),
                'params' => $api->getParams(),
            )))
            {
                if($response['success'])
                {
                    // token
                    $token = $response['token'];

                    // save token
                    craft()->rest->saveAuthenticationToken($handle, $token);

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

    public function actionApisIndex()
    {
        $variables['apis'] = craft()->rest->getApis();

        $this->renderTemplate('rest/apis', $variables);
    }

    public function actionRequestsIndex()
    {
        $variables['requests'] = craft()->rest->getRequests();

        $this->renderTemplate('rest/requests', $variables);
    }

    public function actionDeleteRequest()
    {
       $this->requirePostRequest();
       $this->requireAjaxRequest();

       $id = craft()->request->getRequiredPost('id');

       craft()->rest->deleteRequestById($id);

       $this->returnJson(array('success' => true));
    }

    public function actionSaveRequest()
    {
        $requestId = craft()->request->getParam('requestId');
        $apiHandle = craft()->request->getParam('apiHandle');
        $name = craft()->request->getParam('name');
        $handle = craft()->request->getParam('handle');
        $url = craft()->request->getParam('url');
        $verb = 'get'; // $verb = craft()->request->getParam('verb');
        $format = craft()->request->getParam('format');
        $query = craft()->request->getParam('query');

        if($query)
        {
            $newParams = array();

            foreach($query as $param)
            {
                $newParams[$param['key']] = $param['value'];
            }

            $query = $newParams;
        }
        else
        {
            $query = array();
        }

        if($requestId)
        {
            $request = craft()->rest->getRequestById($requestId);
        }

        if(!isset($request))
        {
            $request = new Rest_RequestModel;
        }

        $request->apiHandle = $apiHandle;
        $request->name = $name;
        $request->handle = $handle;
        $request->verb = $verb;
        $request->format = $format;
        $request->url = $url;
        $request->query = $query;

        if(craft()->rest->saveRequest($request))
        {
            craft()->userSession->setNotice(Craft::t('Request saved.'));
            $this->redirectToPostedUrl();
        }
        else
        {
            craft()->userSession->setError(Craft::t('Couldn’t save request.'));

            // Send the request back to the template
            craft()->urlManager->setRouteVariables(array(
                'request' => $request
            ));
        }

    }

    public function actionEditRequest(array $variables = array())
    {
        $variables['isNew'] = false;

        if (!empty($variables['requestId']))
        {
            if (empty($variables['request']))
            {
                $variables['request'] = craft()->rest->getRequestById($variables['requestId']);

                if (!$variables['request'])
                {
                    throw new HttpException(404);
                }
            }

            $variables['title'] = $variables['request']->name;
        }
        else
        {
            if (empty($variables['request']))
            {
                $variables['request'] = new Rest_RequestModel();
                $variables['isNew'] = true;
            }

            $variables['title'] = Craft::t('Create a new request');
        }

        $variables['apis'] = craft()->rest->getApis();

        $this->renderTemplate('rest/requests/_edit', $variables);
    }
}