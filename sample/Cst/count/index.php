<?php

require_once '../../../vendor/autoload.php';

use Sample\Cst\Classes\Cst;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    var_dump(
        (new Cst())->count(
            [
//                [
//                    "column" => "csttipo",
//                    "value"  => 1
//                ]
            ]
        )
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
