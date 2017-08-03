<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\Empresa\Classes\Empresa;
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

    $result = Empresa::make([
        "empcodigo" => 264
    ])->search()->toArray(true);


    var_dump(
        $result
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
