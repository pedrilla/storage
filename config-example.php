<?php

return [

    'light' => [

        // Display exceptions
        // Not required - just will be used as false
        'exception' => true,

        'loader' => [
            // Path to app directory
            'path' => realpath(dirname(__FILE__)) . '/app',
            // Application namespace
            'namespace' => 'App',
        ],

        'asset' => [
            // Adding ?_=microtime()
            'underscore' => true,

            // prefix for assets
            'prefix' => '/assets'
        ],
    ],

    'fs' => [

        // Realpath to storage folder
        'path' => realpath(dirname(__FILE__)) . '/www/storage',

        // Url to storage folder
        'url' => 'https://domain/storage',

        // Meta base
        'base' => 'https://domain/'
    ],

    'key' => '{put-key-here}'
];
