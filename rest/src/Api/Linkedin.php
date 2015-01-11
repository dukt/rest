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

class Linkedin extends AbstractApi {

    public function getName()
    {
        return "LinkedIn";
    }

    public function getDefaultQuery()
    {
        return array(
            'format' => 'json'
        );
    }

    public function getProviderHandle()
    {
        return 'linkedin';
    }

    public function getApiUrl()
    {
        return 'https://api.linkedin.com/v1/'; // /people/~?format=json
    }

    public function getScopes()
    {
        return array(
            'r_basicprofile',
            'r_fullprofile',
            'r_emailaddress',
            'r_network',
            'r_contactinfo',
            'rw_nus',
            'rw_company_admin',
            'rw_groups',
            'w_messages',
        );
    }

    public function getAuthorizationMethod()
    {
        return 'oauth2_access_token';
    }
}