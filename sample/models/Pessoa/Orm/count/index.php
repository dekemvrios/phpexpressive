<?php

require_once '../../../../../vendor/autoload.php';

use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;
use Solis\Breaker\Abstractions\TExceptionAbstract;

try {

    require '../../../connection/connection.php';

    var_dump(
        (new Pessoa())->count()
    );

} catch (TExceptionAbstract $exception) {
    echo $exception->toJson();
}
