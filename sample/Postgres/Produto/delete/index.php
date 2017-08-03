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

    //    $instance = Produto::make(
    //        [
    //            'empcodigo' => 264,
    //            'procodigo' => 149
    //        ]
    //    );

    var_dump(
        Produto::make(['empcodigo' => 264])->last()->delete()
    );
} catch (TException $exception) {
    echo $exception->toJson();
}