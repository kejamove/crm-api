<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

//    ; DB_CONNECTION=mysql
//; DB_HOST=sql.freedb.tech
//; DB_PORT=3306
//; DB_DATABASE=freedb_kejascrmdb
//; DB_USERNAME=freedb_victor
//; DB_PASSWORD='#TN47$UxR2qgKrw'
//
//; DB_CONNECTION=mysql
//; DB_HOST=dannykioko.org
//; DB_PORT=3306
//; DB_DATABASE=danncquz_kejacrmapi
//; DB_USERNAME=danncquz_kejacrmuser
//; DB_PASSWORD='penguinset102&*'


    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://kejacrm.netlify.app', 'http://localhost:5174', 'http://localhost:5173'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
