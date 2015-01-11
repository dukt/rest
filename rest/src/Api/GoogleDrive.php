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

namespace Dukt\Rest\Api;

class GoogleDrive extends AbstractApi {

    public function getName()
    {
        return "Drive";
    }

    public function getProviderHandle()
    {
        return 'google';
    }

    public function getApiUrl()
    {
        return 'https://www.googleapis.com/drive/v2/';
    }

    public function getScopes()
    {
        return array(
            'https://www.googleapis.com/auth/drive',
            'https://www.googleapis.com/auth/drive.appdata',
            'https://www.googleapis.com/auth/drive.apps.readonly',
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/drive.metadata.readonly',
            'https://www.googleapis.com/auth/drive.readonly',
            'https://www.googleapis.com/auth/drive.scripts',
        );
    }

    public function getParams()
    {
        return array(
            'approval_prompt' => 'force',
            'access_type' => 'offline',
        );
    }
}
