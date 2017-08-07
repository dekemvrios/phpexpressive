<?php

require_once '../../../vendor/autoload.php';

use Sample\Produto\Classes\Produto;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    var_dump(
        (new Produto())->count(
            [
                [
                    "column" => "empcodigo",
                    "value"  => 264
                ]
            ]
        )
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
