<?php

require_once '../../../vendor/autoload.php';

use Sample\Empresa\Classes\Empresa;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $result = Empresa::make([
        "empcodigo" => 264
    ])->search()->toArray(true);


    var_dump(
        $result
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
