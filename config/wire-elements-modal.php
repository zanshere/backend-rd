<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modal Component Defaults
    |--------------------------------------------------------------------------
    |
    | Configure the default properties for a modal component.
    |
    */

    'defaults' => [
        'closeOnClickAway' => true,
        'closeOnEscape' => true,
        'closeOnEscapeIsForceful' => true,
        'dispatchCloseEvent' => false,
        'maxWidth' => '2xl',
        'maxHeight' => false,
        'alignment' => 'center',
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire components namespace
    |--------------------------------------------------------------------------
    |
    | The namespace where the Livewire modal components are stored.
    |
    */

    'namespace' => 'App\\Livewire\\Modals\\',

    /*
    |--------------------------------------------------------------------------
    | Include JS and CSS from CDN or locally
    |--------------------------------------------------------------------------
    |
    | You can choose to load the required CSS and JS for the modals from a
    | CDN or locally from your application's public folder.
    |
    */

    'load_from_cdn' => false,

    'cdn_url' => 'https://cdn.jsdelivr.net/npm/@wire-elements/modal@3.x.x',

    /*
    |--------------------------------------------------------------------------
    | Modal Route Middleware
    |--------------------------------------------------------------------------
    |
    | The middleware that should be used for modal routes.
    |
    */

    'middleware' => ['web'],
];
