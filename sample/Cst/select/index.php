<?php

require_once '../../../vendor/autoload.php';

use Solis\Breaker\Abstractions\TExceptionAbstract;
use Sample\Cst\Classes\Cst;

try {

    require '../../Database/config.php';

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
