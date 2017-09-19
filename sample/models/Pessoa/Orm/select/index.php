<?php

require_once '../../../../../vendor/autoload.php';

use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;

try {

    require '../../../connection/connection.php';

    $result = (new Pessoa())
        ->select(
            [],
            [
                "orderBy"          => [
                    "column"    => "ID",
                    "direction" => "asc",
                ],
                "withDependencies" => true,
            ]
        ) or die('não foram encontrados registro na persistência para o filtro solicitado');

    foreach ($result as $item) {
        var_dump($item->toArray(true, false));
    }

} catch (TExceptionAbstract $exception) {
    echo $exception->toJson();
}
