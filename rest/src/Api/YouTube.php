<?php

namespace Dukt\Rest\Api;

class YouTube extends AbstractApi {

    public function getName()
    {
        return "YouTube";
    }

    public function getProviderHandle()
    {
        return 'google';
    }

    public function getApiUrl()
    {
        return 'https://www.googleapis.com/youtube/v3/';
    }

    public function getScopes()
    {
        return array(
            "https://www.googleapis.com/auth/yt-analytics-monetary.readonly",
            "https://www.googleapis.com/auth/yt-analytics.readonly",
            "https://www.googleapis.com/auth/youtube",
            "https://www.googleapis.com/auth/youtube.readonly",
            "https://www.googleapis.com/auth/youtube.upload",
            "https://www.googleapis.com/auth/youtubepartner"
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