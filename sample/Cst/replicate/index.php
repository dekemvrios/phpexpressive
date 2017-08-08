<?php

require_once '../../../vendor/autoload.php';

use Solis\Breaker\Abstractions\TExceptionAbstract;
use Sample\Cst\Classes\Cst;

try {

    require '../../Database/config.php';

    $instance = Cst::make(
        [
            "iCstCodigo" => 67,
        ]
    )->search();

    if (!empty($instance)) {
        var_dump($instance->replicate());
    }

} catch (TExceptionAbstract $exception) {
    echo $exception->toJson();
}
