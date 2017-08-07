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

    $instance = NFe::make([
        'iEmpCodigo'    => 263,
        'iNFeSequencia' => 300,
    ])->search();

    if (!empty($instance)) {
        $record = $instance->replicate();

        var_dump($record->toArray());
    }

} catch (TException $exception) {
    echo $exception->toJson();
}
