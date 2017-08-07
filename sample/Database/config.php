<?php

use Solis\Expressive\Classes\Illuminate\Database;

Database::boot(
    [
        'driver'   => 'pgsql',
        'host'     => getenv('DB_HOST'),
        'database' => getenv('DB_NAME'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
    ]
);