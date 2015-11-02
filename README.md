# REST

Perform authenticated REST requests

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
- [GitHub](https://dukt.net/craft/github)
- [Slack](https://dukt.net/craft/slack)
- [Pinterest](https://dukt.net/craft/pinterest)


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
- token