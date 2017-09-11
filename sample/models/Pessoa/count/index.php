<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;
use Solis\Breaker\TException;

try {

    require '../../connection/connection.php';

    var_dump(
        (new Pessoa())->count()
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
