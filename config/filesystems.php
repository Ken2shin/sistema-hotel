<?php

return [

    'default' => env('FILESYSTEM_DISK', 'supabase'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        'supabase' => [
            'driver' => 's3',
            'key' => env('SUPABASE_ACCESS_KEY_ID'),
            'secret' => env('SUPABASE_SECRET_ACCESS_KEY'),
            'region' => env('SUPABASE_REGION', 'us-east-1'),
            'bucket' => env('SUPABASE_STORAGE_BUCKET'),
            'endpoint' => env('SUPABASE_S3_ENDPOINT'),
            'url' => env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_STORAGE_BUCKET'),
            'use_path_style_endpoint' => true,
            'visibility' => 'public',
            'throw' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];