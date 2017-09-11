<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;
use Solis\Breaker\TException;

try {

    require '../../connection/connection.php';

    $last = (new Pessoa())->last() or die('nao há registros cadastrados na base de dados');

    $last->nome = 'UPDATE TEST [' . uniqid(rand()) . ']';

    var_dump(
            $last->patch()
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
