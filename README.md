# REST

Perform authenticated REST requests

## Introduction

- Supported Providers
- Supported APIs

## Requests

### Simple Request

    {% set response = craft.rest.request.url('https://www.googleapis.com/youtube/v3/search').send() %}

### Query Parameters

    {% set response = craft.rest.request
        .url('https://www.googleapis.com/youtube/v3/search')
        .query({
            part: 'snippet',
            q: 'timelapse',
        })
        .send() %}

### Authentication

    {% set response = craft.rest.request
        .authentication('youtube')
        .url('https://www.googleapis.com/youtube/v3/search')
        .query({
            part: 'snippet',
            q: 'timelapse',
        })
        .send() %}

### API

    {% set response = craft.rest.request
        .authentication('google')
        .api('youtube')
        .url('search')
        .query({
            part: 'snippet',
            q: 'timelapse',
        })
        .send() %}

## Authentications

### Supported Authentication Providers

The following authentication providers are supported:

- Facebook <small>— Native</small>
- Google <small>— Native</small>
- Twitter <small>— Native</small>
- [GitHub](https://dukt.net/craft/github)

REST plugin can have a token per OAuth provider and per API.

OAuth provider-based authentications will not have any scope by default. The scope needs to be manually set before connecting.

API-based authentications will have a default scopes. Scopes need to be enabled to be taken into account.
The default scope can also be extended with custom scopes.

## APIs

- Facebook
- GitHub
- Google Analytics
- Twitter
- YouTube
- YouTube Analytics

### Supported APIs

### Creating a custom API

youtube/apis/YouTube.php

    <?php

    namespace Dukt\Apis;

    use Craft\UrlHelper;

    class YouTube extends Api
    {
        public function getName()
        {
            return 'YouTube';
        }

        public function getBaseUrl()
        {
            return 'https://www.googleapis.com/youtube/'.$this->getVersion().'/';
        }

        public function getIconUrl()
        {
            return UrlHelper::getResourceUrl('youtube/svg/youtube.svg');
        }

        public function getVersion()
        {
            return 'v3';
        }

        public function getOAuthProviderHandle()
        {
            return 'google';
        }

        public function getAvailableScopes()
        {
            return [
                'https://www.googleapis.com/auth/youtube',
                'https://www.googleapis.com/auth/youtube.force-ssl',
                'https://www.googleapis.com/auth/youtube.readonly',
                'https://www.googleapis.com/auth/youtube.upload',
                'https://www.googleapis.com/auth/youtubepartner',
                'https://www.googleapis.com/auth/youtubepartner-channel-audit',
            ];
        }
    }

youtube/YouTubePlugin.php

    <?php

    namespace Craft;

    class YoutubePlugin extends BasePlugin
    {
        /**
         * Get Name
         */
        public function getName()
        {
            return Craft::t('YouTube');
        }

        /**
         * Get APIs
         */
        public function getApis()
        {
            require_once(CRAFT_PLUGINS_PATH.'youtube/apis/Youtube.php');

            return [
                'Dukt\Apis\YouTube'
            ];
        }

        ...

    }


## API Reference

### Rest_AuthenticationModel

- id
- tokenId
- authenticationHandle
- scopes
- customScopes

### Rest_RequestCriteriaModel

- handle
- url
- uri
- verb
- format
- headers
- query
- authentication
- api