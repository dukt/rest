<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/rest/blob/v1/LICENSE.md
 */

namespace Craft;

class Rest_AuthenticationProviderModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'oauthProviderHandle' => AttributeType::String,
        );
    }

    public function getName()
    {
        return $this->getOauthProvider()->getName();
    }

    public function getIconUrl()
    {
        return $this->getOauthProvider()->getIconUrl();
    }

    public function getHandle()
    {
        return $this->getOauthProvider()->getHandle();
    }

    public function getOauthProvider()
    {
        return craft()->oauth->getProvider($this->oauthProviderHandle, false);
    }

    public function getScope()
    {
        $authorizationOptions = craft()->config->get($this->getHandle().'Scope', 'rest');

        return $authorizationOptions;
    }

    public function getAuthorizationOptions()
    {
        $authorizationOptions = craft()->config->get($this->getHandle().'AuthorizationOptions', 'rest');

        return $authorizationOptions;
    }
}