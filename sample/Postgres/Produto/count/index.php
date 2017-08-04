<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\Produto\Classes\Produto;
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
        (new Produto())->count(
            [
                [
                    "column" => "empcodigo",
                    "value"  => 264
                ]
            ]
        )
    );

} catch (TException $exception) {
    echo $exception->toJson();
}