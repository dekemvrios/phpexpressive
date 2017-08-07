<?php

require_once '../../../vendor/autoload.php';

use Solis\Breaker\Abstractions\TExceptionAbstract;
use Sample\Empresa\Classes\Empresa;

try {

    require '../../Database/config.php';

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
            'withProperties'   => [
                'empcodigo',
                //'empnome',
                //'produtos',
            ],
        ]
    );

    // Display Script End time
    $time_end = microtime(true);

    //dividing with 60 will give the execution time in minutes other wise seconds
    $execution_time = ($time_end - $time_start);///60;

    //execution time of the script
    echo '<b>Total Execution Time:</b> ' . $execution_time . ' Segs';

} catch (TExceptionAbstract $exception) {
    echo $exception->toJson();
}

