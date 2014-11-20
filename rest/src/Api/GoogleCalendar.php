<?php

namespace Dukt\Rest\Api;

class GoogleCalendar extends AbstractApi {

    public function getName()
    {
        return "Google Calendar";
    }

    public function getProviderHandle()
    {
        return 'google';
    }

    public function getApiUrl()
    {
        return 'https://www.googleapis.com/calendar/v3/';
    }

    public function getScopes()
    {
        return array(
            'https://www.googleapis.com/auth/calendar'
        );
    }
}