<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ImageKit Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for ImageKit integration.
    | You can get these values from your ImageKit dashboard.
    |
    */

    'public_key' => env('IMAGEKIT_PUBLIC_KEY'),
    'private_key' => env('IMAGEKIT_PRIVATE_KEY'),
    'url_endpoint' => env('IMAGEKIT_URL_ENDPOINT'),
];
