<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\NFe\Classes\NFe;
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
        NFe::make(
            [
                "iEmpCodigo" => 264,
            ]
        )->last()->delete()
    );
} catch (TException $exception) {
    echo $exception->toJson();
}