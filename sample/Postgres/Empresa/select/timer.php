<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Sample\Postgres\Empresa\Classes\Empresa;

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

    //place this before any script you want to calculate time
    $time_start = microtime(true);

    $result = (new Empresa())->select(
        [
//            [
//                "column" => "empcodigo",
//                "value"  => 264,
//            ],
        ],
        [
            "withDependencies" => true,
        ]
    );

    // Display Script End time
    $time_end = microtime(true);

    //dividing with 60 will give the execution time in minutes other wise seconds
    $execution_time = ($time_end - $time_start);///60;

    //execution time of the script
    echo '<b>Total Execution Time:</b> '.$execution_time.' Segs';

} catch (TExceptionAbstract $exception) {
    echo $exception->toJson();
}

