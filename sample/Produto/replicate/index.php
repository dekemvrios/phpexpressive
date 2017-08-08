<?php

require_once '../../../vendor/autoload.php';

use Sample\Produto\Classes\Produto;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $instance = Produto::make([
        'iEmpCodigo' => 263,
    ])->last();

    if (!empty($instance)) {
        var_dump($instance->replicate());
    }

} catch (TException $exception) {
    echo $exception->toJson();
}
