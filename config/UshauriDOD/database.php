<?php

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'dod_db_host'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'dod_db_name'),
            'username' => env('DB_USERNAME', 'dod_db_user'),
            'password' => env('DB_PASSWORD', 'dod_db_password'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
            'engine' => null,
        ],

        // Other database connections...

    ],

    // ...

];
