<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Sample\Postgres\Cst\Classes\Cst;

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

} catch (TExceptionAbstract $exception) {
    echo $exception->toJson();
}
