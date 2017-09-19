<?php

require_once '../../../../../vendor/autoload.php';

use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;
use Solis\Breaker\TException;

try {

    require '../../../connection/connection.php';

    $last = (new Pessoa())->last() or die('nao há registros cadastrados na base de dados');

    $instance = Pessoa::make([
            'proID' => $last->ID,
    ]);

    $instance = $instance->search() or die('registro nao encontrado na base de dados');

    echo json_encode($instance->toArray());

} catch (TException $exception) {
    echo $exception->toJson();
}
