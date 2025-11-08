<?php

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
            'serve' => false,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        'images' => [
            'driver' => 'local',
            'root' => storage_path('app/images'),
            'url' => env('APP_URL') . '/img',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],
        'certificates' => [
            'driver' => 'local',
            'root' => storage_path('app/certificates'),
            'url' => env('APP_URL') . '/certificates',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],
        'documents' => [
            'driver' => 'local',
            'root' => storage_path('app/documents'),
            'url' => env('APP_URL') . '/documents',
            'visibility' => 'public',
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
        //public_path('img') => storage_path('app/images'),
        public_path('img/eventos') => storage_path('app/images/eventos'),
        public_path('img/empleos') => storage_path('app/images/empleos'),
        public_path('img/avatars') => storage_path('app/images/avatars'),
        public_path('img/noticias') => storage_path('app/images/noticias'),
        public_path('img/sesiones') => storage_path('app/images/sesiones'),
        public_path('certificates') => storage_path('app/certificates'),
        public_path('documents') => storage_path('app/documents'),
    ],

];