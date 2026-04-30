<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Laravel only applies CORS to URLs that match "paths" below. Lighthouse
    | serves GraphQL at /graphql, which is NOT under api/* by default — add it
    | here or browsers will block cross-origin XHR/fetch from your frontend.
    |
    */

    'paths' => [
        'api/*',
        'graphql',
        'graphql/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', false),

];
