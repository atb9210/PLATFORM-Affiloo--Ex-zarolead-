<?php

use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'orders' => [
            'driver' => 'single',
            'path' => storage_path('logs/orders.log'),
            'level' => 'debug',
        ],

        // DEBUG
        'postback' => [
            'driver' => 'single',
            'path' => storage_path('logs/postback.log'),
            'level' => 'debug',
        ],

        // DEBUG
        'facebook' => [
            'driver' => 'single',
            'path' => storage_path('logs/facebook.log'),
            'level' => 'debug',
        ],

        // DEBUG
        'facebook_monitor' => [
            'driver' => 'single',
            'path' => storage_path('logs/facebook_monitor.log'),
            'level' => 'debug',
        ],

        // DEBUG
        'tiktok' => [
            'driver' => 'single',
            'path' => storage_path('logs/tiktok.log'),
            'level' => 'debug',
        ],

        // DEBUG
        'elogy' => [
            'driver' => 'single',
            'path' => storage_path('logs/elogy.log'),
            'level' => 'debug',
        ],

        // DEBUG
        'dropin' => [
            'driver' => 'single',
            'path' => storage_path('logs/dropin.log'),
            'level' => 'debug',
        ],

        // DEBUG
        'chatapi' => [
            'driver' => 'single',
            'path' => storage_path('logs/chatapi.log'),
            'level' => 'debug',
        ],
    ],

];
