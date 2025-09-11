<?php

// Config for Attargah/AntiScam
return [

    // Secret key used for hashing and validation
    'key' => null,

    'scam' => [

        'active' => true,

        //Permanently ban scam IPs (Not Recommended)
        'ban' => false,

        //if true, scam ips will be saved to the database
        'save_log' => true,

        // If true, logs will be shown in the admin panel (resource)
        'register_logs_to_panel' => true,

        // Hidden inputs added to the form to detect bots
        'inputs' => [
            [
                'id' => 'name',
                'label' => 'Name',
                'name' => 'name',
            ],
            [
                'id' => 'subject',
                'label' => 'Subject',
                'name' => 'subject'
            ],
            [
                'id' => 'message',
                'label' => 'Message',
                'name' => 'message'
            ],
            [
                'id' => 'email',
                'label' => 'Email',
                'name' => 'email'
            ],
            [
                'id' => 'phone',
                'label' => 'Phone Number',
                'name' => 'phone'
            ],
        ],

        // Randomize the order of inputs (true/false)
        'order_random' => true,

        'display' => [
            // If true, hidden inputs are visually hidden via CSS
            'active' => false,

            // CSS rules for hide inputs
            'css' => 'display:none!important',
        ],

        // Default attributes applied to fake input elements
        'attributes' => [
            'input' => 'aria-hidden="-1" tab-index="-1"',
            'label' => 'aria-hidden="-1" tab-index="-1"',
            'div' => 'aria-hidden="-1" tab-index="-1"',
        ],

        'off_screen' => [
            // If true, hidden inputs are moved off-screen
            'active' => true,

            // CSS rules applied to move fake inputs outside the viewport
            'css' => 'position:absolute!important; left:-9999px!important; z-index:-9999!important;',
        ]
    ],

    'spam' => [

        'active' => true,

        // Maximum number of requests allowed within the time window
        'max_requests_per_window' => 5,

        // Time window length (in seconds) for counting requests
        'window_in_seconds' => 60,

        // Multiplier applied to ban duration (progressive increase)
        'ban_duration_multiplier' => 3,

        // If ban duration exceeds this value (in minutes), ban becomes permanent
        'permanent_ban_threshold_min' => 10080,
    ],

];
