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

    $result = (new Cst())
        ->select(
            [],
            [
                "orderBy" => [
                    "column"    => "cstcodigo",
                    "direction" => "asc",
                ],
                "limit"   => [
                    "number" => 1,
                    //"offset" => 10
                ],
            ]
        );


    if (!empty($result)) {
        if (is_array($result)) {
            foreach ($result as $item) {
                var_dump($item->toArray());
            }
        } else {
            var_dump($result->toArray());
        }
    }

} catch (TExceptionAbstract $exception) {
    echo $exception->toJson();
}
