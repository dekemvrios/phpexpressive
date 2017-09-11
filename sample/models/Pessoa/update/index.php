<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;
use Solis\Breaker\TException;

try {

    require '../../connection/connection.php';

    $instance = Pessoa::make([])->last() or die('não há registros cadastrados na base de dados');

    $instance->nome = 'UPDATE TEST [' . uniqid(rand()) . ']';

    $instance->proCidade = [
            "proID"     => 1,
            "proNome"   => "Rio do Sul",
            "proIbgeId" => "3",
    ];

    $instance->update() or die('erro ao realizar update do registro');

    echo json_encode($instance->last()->toArray());

} catch (TException $exception) {
    echo $exception->toJson();
}