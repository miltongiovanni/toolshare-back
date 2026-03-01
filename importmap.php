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
    'category' => [
        'path' => './assets/js/pages/category.js',
        'entrypoint' => true,
    ],
    'subcategory' => [
        'path' => './assets/js/pages/subcategory.js',
        'entrypoint' => true,
    ],
    'user' => [
        'path' => './assets/js/pages/user.js',
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
    'dropzone' => [
        'version' => '6.0.0-beta.2',
    ],
    'just-extend' => [
        'version' => '5.1.1',
    ],
    'cropperjs' => [
        'version' => '2.1.0',
    ],
    '@cropper/utils' => [
        'version' => '2.1.0',
    ],
    '@cropper/elements' => [
        'version' => '2.1.0',
    ],
    '@cropper/element' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-canvas' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-image' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-shade' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-handle' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-selection' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-grid' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-crosshair' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-viewer' => [
        'version' => '2.1.0',
    ],
    'jszip' => [
        'version' => '3.10.1',
    ],
    'datatables.net-bs5' => [
        'version' => '2.3.5',
    ],
    'datatables.net' => [
        'version' => '2.3.5',
    ],
    'datatables.net-bs5/css/dataTables.bootstrap5.min.css' => [
        'version' => '2.3.5',
        'type' => 'css',
    ],
    'datatables.net-buttons-bs5' => [
        'version' => '3.2.6',
    ],
    'datatables.net-buttons' => [
        'version' => '3.2.6',
    ],
    'datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css' => [
        'version' => '3.2.6',
        'type' => 'css',
    ],
    'datatables.net-plugins/i18n/fr-FR.mjs' => [
        'version' => '2.3.6',
    ],
    'datatables.net-plugins/i18n/es-CO.mjs' => [
        'version' => '2.3.6',
    ],
    'datatables.net-plugins/i18n/en-GB.mjs' => [
        'version' => '2.3.6',
    ],
    'dropzone/dist/dropzone.css' => [
        'version' => '6.0.0-beta.2',
        'type' => 'css',
    ],
    'luxon' => [
        'version' => '3.7.2',
    ],
    'bootstrap-icons/font/bootstrap-icons.min.css' => [
        'version' => '1.13.1',
        'type' => 'css',
    ],
];
