<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\Empresa\Classes\Empresa;
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

    $result = (new Empresa())->select(
        [
            [
                "column" => "empcodigo",
                "value"  => 264,
            ],
        ],
        [
            "withDependencies" => [
                'produtos',
            ],
//            'withProperties'   => [
//                'empcodigo',
//                //'empnome',
//                //'produtos',
//            ],
        ]
    );

    if (!empty($result)) {

        $result = !is_array($result) ? [$result] : $result;

        foreach ($result as $item) {
            var_dump(
                $item->toArray()
            );
        }
    }

} catch (TException $exception) {
    echo $exception->toJson();
}

