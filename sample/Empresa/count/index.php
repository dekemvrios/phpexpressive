<?php

require_once '../../../vendor/autoload.php';

use Sample\Empresa\Classes\Empresa;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    var_dump(
        (new Empresa())->count([])
    );

} catch (TException $exception) {
    echo $exception->toJson();
}