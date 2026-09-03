<?php

declare(strict_types=1);

return [
    'table' => env('TRANSLATION_TABLE', 'translations'),

    'models' => [
        'translation' => env('TRANSLATION_MODEL', Karnoweb\Translation\Models\Translation::class),
    ],

    'cache' => [
        'enabled' => env('TRANSLATION_CACHE_ENABLED', true),
    ],
];
