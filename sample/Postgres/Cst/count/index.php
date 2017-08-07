<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\Cst\Classes\Cst;
use Solis\Breaker\TException;

try {

    Database::boot(
        [
            'driver'   => 'pgsql',
            'host'     => 'database',
            'database' => 'empresarial',
            'username' => 'postgres',
            'password' => '4hvU1kbzGe',
        ]
    );

    var_dump(
        (new Cst())->count(
            [
//                [
//                    "column" => "csttipo",
//                    "value"  => 1
//                ]
            ]
        )
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
