<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\Produto\Classes\Produto;
use Solis\Breaker\TException;

try {

    $GLOBALS['aConfig'] = [
        'db' => [
            'driver'   => 'pgsql',
            'host'     => "database",
            'database' => 'empresarial',
            'username' => 'postgres',
            'password' => '4hvU1kbzGe',
        ]
    ];

    Database::boot();

    $instance = Produto::make(
        [
            'procodigo' => 163,
            'empcodigo' => 264
        ]
    )->search();
    if (!empty($instance)) {
        var_dump(
            $instance->toArray()
        );
    }

} catch (TException $exception) {
    echo $exception->toJson();
}
