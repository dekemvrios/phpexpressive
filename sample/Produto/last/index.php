<?php

require_once '../../../vendor/autoload.php';

use Sample\Produto\Classes\Produto;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    var_dump(
        //(new Produto())->last()
        Produto::make(['empcodigo' => 264])->last()
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
