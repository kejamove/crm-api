<?php
// config/custom.php

return [
    'email_setup' => [
        'host' => env('CUSTOM_MAIL_HOST', ''),
        'port' => env('CUSTOM_MAIL_PORT', ''),
        'username' => env('CUSTOM_MAIL_USERNAME', ''),
        'password' => env('CUSTOM_MAIL_PASSWORD', ''),
        'encryption' => env('CUSTOM_MAIL_ENCRYPTION', ''),
        'from_address' => env('CUSTOM_MAIL_FROM_ADDRESS', ''),
        'from_name' => env('CUSTOM_MAIL_FROM_NAME', ''),
    ],
];
