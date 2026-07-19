<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Installer Engine Configuration
    |--------------------------------------------------------------------------
    |
    | Controls the web installer wizard behaviour.
    |
    */

    // File written after successful install to bypass wizard
    'lock_file' => storage_path('installed'),

    // Minimum PHP version required
    'php_version' => '8.3.0',

    // Required PHP extensions
    'extensions' => [
        'pdo',
        'pdo_mysql',
        'mbstring',
        'openssl',
        'tokenizer',
        'xml',
        'ctype',
        'json',
        'bcmath',
        'fileinfo',
    ],

    // Routes prefix for installer
    'route_prefix' => 'install',
];
