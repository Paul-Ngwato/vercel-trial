<?php

/*
| The public disk (uploaded photos) picks its driver from PUBLIC_DISK:
|   s3     — an S3-compatible bucket (set the AWS_* variables too)
|   db     — keep bytes in the file_storage table (used automatically on
|            Vercel, whose runtime filesystem is read-only)
|   local  — storage/app/public, served through the public/storage link
*/
$publicDisk = env('PUBLIC_DISK') ?: 'local';
if (($publicDisk !== 's3' || ! env('AWS_ACCESS_KEY_ID')) && env('VERCEL')) {
    $publicDisk = 'db'; // read-only filesystem — keep uploads in the database
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => false, // /storage/{path} is handled by routes/web.php instead
            'throw' => false,
            'report' => false,
        ],

        // Uploaded photos. Locally this is storage/app/public (via the public/storage
        // symlink). On Vercel the filesystem is read-only, so set PUBLIC_DISK=s3 to keep
        // uploads in an S3-compatible bucket (AWS S3, Cloudflare R2, Supabase Storage...).
        'public' => $publicDisk === 's3'
            ? [
                'driver' => 's3',
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION'),
                'bucket' => env('AWS_BUCKET'),
                'url' => env('AWS_URL'),
                'endpoint' => env('AWS_ENDPOINT'),
                'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
                'throw' => false,
                'report' => false,
            ]
            : ($publicDisk === 'db'
                ? [
                    'driver' => 'dbfiles',
                    'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
                    'throw' => false,
                    'report' => false,
                ]
                : [
                    'driver' => 'local',
                    'root' => storage_path('app/public'),
                    'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
                    'visibility' => 'public',
                    'throw' => false,
                    'report' => false,
                ]),

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
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
