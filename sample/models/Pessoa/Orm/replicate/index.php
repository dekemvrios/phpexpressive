<?php

require_once '../../../../../vendor/autoload.php';

use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;

try {

    require '../../../connection/connection.php';


    $last = (new Pessoa())->last() or die('nao há registros cadastrados na base de dados');

    $replicated = $last->replicate();

    echo json_encode($replicated->toArray());

} catch (TExceptionAbstract $exception) {
    echo $exception->toJson();
}
