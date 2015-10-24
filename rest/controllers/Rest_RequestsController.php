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

class Rest_RequestsController extends BaseController
{
    public function actionIndex()
    {
        $variables['requests'] = craft()->rest_requests->getRequests();

        $this->renderTemplate('rest/requests', $variables);
    }

    public function actionEdit(array $variables = array())
    {
        $variables['isNew'] = false;

        if (!empty($variables['requestId']))
        {
            if (empty($variables['request']))
            {
                $variables['request'] = craft()->rest_requests->getRequestById($variables['requestId']);

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

        $variables['authentications'] = craft()->rest_authentications->getAuthentications();

        $this->renderTemplate('rest/requests/_edit', $variables);
    }

    public function actionSave()
    {
        $requestId = craft()->request->getParam('requestId');
        $authenticationHandle = craft()->request->getParam('authenticationHandle');
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
            $request = craft()->rest_requests->getRequestById($requestId);
        }

        if(!isset($request))
        {
            $request = new Rest_RequestModel;
        }

        $request->authenticationHandle = $authenticationHandle;
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

    public function actionDelete()
    {
       $this->requirePostRequest();
       $this->requireAjaxRequest();

       $id = craft()->request->getRequiredPost('id');

       craft()->rest_requests->deleteRequestById($id);

       $this->returnJson(array('success' => true));
    }

}