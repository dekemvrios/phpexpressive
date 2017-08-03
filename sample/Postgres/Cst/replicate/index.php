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

    $instance = Cst::make([
        "iCstCodigo" => 67,
    ])->search();

    if (!empty($instance)) {
        var_dump($instance->replicate());
    }

} catch (TException $exception) {
    echo $exception->toJson();
}
