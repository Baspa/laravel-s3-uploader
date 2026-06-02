<?php

// config for Baspa/LaravelS3Client
return [

    /*
    |--------------------------------------------------------------------------
    | Default disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk the package uploads to. This should point at an S3
    | (or S3-compatible) disk defined in your application's config/filesystems.php,
    | configured through the usual AWS_* environment variables.
    |
    | You can override it per command run with the --disk option.
    |
    */

    'disk' => env('S3_CLIENT_DISK', 's3'),

];
