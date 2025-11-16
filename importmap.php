<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'auth' => [
        'path' => './assets/auth.js',
        'entrypoint' => true,
    ],
    'sidebar-menu' => [
        'path' => './assets/js/sidebar-menu.js',
        'entrypoint' => true,
    ],
    'custom-card' => [
        'path' => './assets/js/custom-card/custom-card.js',
        'entrypoint' => true,
    ],
    'counter-custom' => [
        'path' => './assets/js/counter/counter-custom.js',
        'entrypoint' => true,
    ],
    'height-equal' => [
        'path' => './assets/js/height-equal.js',
        'entrypoint' => true,
    ],
    'dashboard' => [
        'path' => './assets/js/dashboard/default.js',
        'entrypoint' => true,
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    'bootstrap' => [
        'version' => '5.3.8',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.8',
        'type' => 'css',
    ],
    'feather-icons' => [
        'version' => '4.29.2',
    ],
    'clipboard' => [
        'version' => '2.0.11',
    ],
];
