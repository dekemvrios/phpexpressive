<?php

require_once '../../../vendor/autoload.php';

use Sample\Empresa\Classes\Empresa;
use Solis\Expressive\Classes\Illuminate\Diglett;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

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

    Diglett::disable();

} catch (TException $exception) {
    echo $exception->toJson();
}

