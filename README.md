# Craft REST

Perform authenticated REST requests

-------------------------------------------

## Requirements

- Craft 2.5
- Craft OAuth 1.0

## Installation

1. Download the latest release of the plugin
2. Drop the `rest` plugin folder to `craft/plugins`
3. Install REST plugin from the control panel in `Settings > Plugins`

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

### Token


    {% set response = craft.oauth.getTokenById(123) %}

    {% set response = craft.rest.request
        .token(token)
        .url('https://www.googleapis.com/youtube/v3/search')
        .query({
            part: 'snippet',
            q: 'timelapse',
        })
        .send() %}

## Authentications

The following authentication providers are supported:

- Facebook <small>— Native</small>
- Google <small>— Native</small>
- Twitter <small>— Native</small>
- Vimeo <small>— Native</small>
- [Slack](https://github.com/dukt/craft-slack)


## Links

- [REST Plugin Overview](https://dukt.net/craft/rest/)
- [REST Documentation](https://dukt.net/craft/rest/docs)

[Dukt.net](https://dukt.net/) © 2015 - All rights reserved