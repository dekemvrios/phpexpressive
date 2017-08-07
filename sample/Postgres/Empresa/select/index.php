<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\Empresa\Classes\Empresa;
use Solis\Expressive\Classes\Illuminate\Diglett;
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

    Diglett::enable(1);

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

