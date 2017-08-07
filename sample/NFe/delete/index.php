<?php

require_once '../../../vendor/autoload.php';

use Sample\NFe\Classes\NFe;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    var_dump(
        NFe::make(
            [
                "iEmpCodigo" => 263,
            ]
        )->last()->delete()
    );
} catch (TException $exception) {
    echo $exception->toJson();
}