<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Theme Engine Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Theme Engine. Controls active theme and fallback.
    |
    */
    'active' => env('THEME_ACTIVE', 'default'),
    'fallback' => 'default',
    'path' => base_path('themes'),
];
