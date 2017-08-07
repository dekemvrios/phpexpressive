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

    $result = NFe::make(
        [
            "iNFeSequencia" => 15,
            "iEmpCodigo"    => 264
        ]
    )->search()->toArray(true);

    var_dump(
        $result
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
