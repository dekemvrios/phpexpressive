<?php

require_once '../../../vendor/autoload.php';

use Sample\NFe\Classes\NFe;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $result = NFe::make(
        [
            "iNFeSequencia" => 15,
            "iEmpCodigo"    => 264
        ]
    )->search()->toArray(true);

    var_dump(
        $result
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
