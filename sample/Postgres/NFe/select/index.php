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

    $result = (new NFe())->select(
        [
            [
                "column" => "empcodigo",
                "value"  => 264,
            ],
        ], [
            'limit'   => [
                'number' => 10,
                'offset' => 10,
            ],
            'orderBy' => [
                'column'    => 'nfesequencia',
                'direction' => 'desc',
            ],

            'withProperties' => [
                'nfesequencia',
                'empcodigo',
                'nfenumero',
                'nfeserie',
                'itemnfe',
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

