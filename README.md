# Craft REST

Perform authenticated REST requests

-------------------------------------------

## Table of Contents

- [Installing and updating](#installing-and-updating)
    - [Requirements](#requirements)
    - [Installation](#installation)
    - [Updating](#updating)
- [Requests](#requests)
    - [Simple Request](#simple-request)
    - [Query Parameters](#query-parameters)
    - [Authentication](#authentication)
    - [Token](#token)
- [Authentications](#authentications)

## Installing and updating

### Requirements

- Craft 2.5
- Craft OAuth 1.0

### Installation

1. Download the latest release of the plugin
2. Drop the `rest` plugin folder to `craft/plugins`
3. Install REST plugin from the control panel in `Settings > Plugins`

### Updating

1. Download the latest release of the plugin
2. Replace the `rest` plugin folder by the new one under `craft/plugins`
3. Access your Craft control panel. You might be prompted to "Finish Up" the update if one or more migrations need to be applied.

## Requests

### Simple Request

    {% set response = craft.rest.request.url('http://api.openweathermap.org/data/2.5/weather?q=London,uk&appid=2de143494c0b295cca9337e1e96b00e0').send() %}

    <pre>{{ dump(response) }}</pre>

### Query Parameters

    {% set response = craft.rest.request
        .url('http://api.openweathermap.org/data/2.5/weather')
        .query({
            q: 'London,uk',
            appid: '2de143494c0b295cca9337e1e96b00e0',
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

### Supported Providers

You can set up authentication with any provider supported by Craft OAuth.

[See All Craft OAuth Providers](https://dukt.net/craft/oauth/docs/providers)

#### Built-in

- Facebook
- Google
- Twitter
- Vimeo

#### Third-Party

- [GitHub](https://github.com/dukt/craft-github) by [Dukt](https://dukt.net/)
- [Slack](https://github.com/dukt/craft-slack) by [Dukt](https://dukt.net/)
