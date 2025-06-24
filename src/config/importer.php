<?php

return [
    'storage_path' => env('IMPORTER_STORAGE_PATH', storage_path('app/imported')),
    'cache_enabled' => true,
    'cache_prefix' => 'imported.',
];
