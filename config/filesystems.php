<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
            'throw'  => false,
        ],

        'logos' => [
            'driver' => 'local',
            'root'   => env('LOGO_PATH', public_path() . '/logo'),
        ],

        'documents' => [
            'driver' => 'local',
            'root'   => storage_path() . '/documents',
        ],

        's3' => [
            'driver'                  => 's3',
            'key'                     => env('S3_KEY', ''),
            'secret'                  => env('S3_SECRET', ''),
            'region'                  => env('S3_REGION', 'us-east-1'),
            'bucket'                  => env('S3_BUCKET', ''),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw'                   => false,
        ],

        'rackspace' => [
            'driver'    => 'rackspace',
            'username'  => env('RACKSPACE_USERNAME', ''),
            'key'       => env('RACKSPACE_KEY', ''),
            'container' => env('RACKSPACE_CONTAINER', ''),
            'endpoint'  => env('RACKSPACE_ENDPOINT', 'https://identity.api.rackspacecloud.com/v2.0/'),
            'region'    => env('RACKSPACE_REGION', 'IAD'),
            'url_type'  => env('RACKSPACE_URL_TYPE', 'publicURL'),
        ],

        'gcs' => [
            'driver' => 'gcs',
            'bucket' => env('GCS_BUCKET', 'cloud-storage-bucket'),
            //'service_account'                      => env('GCS_USERNAME', ''),
            //'service_account_certificate'          => storage_path() . '/credentials.p12',
            //'service_account_certificate_password' => env('GCS_PASSWORD', ''),
            'project_id' => env('GCS_PROJECT_ID'),
            'key_file'   => storage_path() . '/gcs-credentials.json',
        ],
    ],
];
