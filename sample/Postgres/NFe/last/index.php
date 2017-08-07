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

    $last = NFe::make(
        [
            'iEmpCodigo' => 263,
        ]
    )->last() or die('not found last record for NFe');

    var_dump(
        $last
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
