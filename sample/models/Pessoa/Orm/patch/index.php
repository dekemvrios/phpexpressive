<?php

require_once '../../../../../vendor/autoload.php';

use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;
use Solis\Breaker\TException;

try {

    require '../../../connection/connection.php';

    $last = (new Pessoa())->last() or die('nao há registros cadastrados na base de dados');

    $total = 1;
    for ($i = 0; $i < $total; $i++) {
        $endereco[] = [

            "proLogradouro" => "Rua - " . uniqid(rand()),
            "proCidade"     => "Cidade - " . uniqid(rand()),
            "proEstado"     => uniqid(rand()),

        ];
    }

    $last->proNome     = 'PATCH TEST [' . uniqid(rand()) . ']';
    $last->proEndereco = $endereco;

    $last = $last->patch() or die('erro ao processar operação de patch');

    var_dump(
        $last->toArray()
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
