<?php

return array(
    // 'oauthScopes' => [
    //     'instagram' => [
    //         'publi',
    //         'comments',
    //         'relationships',
    //         'likes',
    //     ]
    // ]

    'authorizationOptions' => [
        'google' => [
            'access_type' => 'offline',
            'approval_prompt' => 'force'
        ]
    ]
);
