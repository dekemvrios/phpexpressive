<?php

use Solis\Expressive\Classes\Illuminate\Database;

Database::boot([
        'driver'   => getenv('DB_DRIVER'),
        'host'     => getenv('DB_HOST'),
        'database' => getenv('DB_NAME'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
]);