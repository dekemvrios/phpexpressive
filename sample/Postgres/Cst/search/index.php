<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\Cst\Classes\Cst;
use Solis\Breaker\TException;

try {

    Database::boot([
        'driver'   => 'pgsql',
        'host'     => 'database',
        'database' => 'empresarial',
        'username' => 'postgres',
        'password' => '4hvU1kbzGe',
    ]);

    $instance = Cst::make([
        'cstcodigo' => 167,
    ]);

    $record = $instance->search();
    var_dump(
        !empty($record) ? $record->toArray() : $record
    );


} catch (TException $exception) {
    echo $exception->toJson();
}
