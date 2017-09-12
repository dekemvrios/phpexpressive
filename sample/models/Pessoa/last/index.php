<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;
use Solis\Breaker\Abstractions\TExceptionAbstract;

try {

    require_once '../../connection/connection.php';

    $last = (new Pessoa())->last() or die('não há registros cadastrados na base de dados');

    echo json_encode($last->toArray());

} catch (TExceptionAbstract $exception) {
    echo $exception->toJson();
}
