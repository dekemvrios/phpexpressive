<?php

require_once '../../../../../vendor/autoload.php';

use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;
use Solis\Breaker\Abstractions\TExceptionAbstract;

try {

    require_once '../../../connection/connection.php';

    $Pessoa = Pessoa::make([
            "proNome"             => 'Fulano - ' . uniqid(rand()),
            "proInscricaoFederal" => '' . rand(11111111111111, 99999999999999) . '',
            "proTipo"             => 1,
        // dependencia declarada como relacionamento na base de dados
            "proEndereco"         => [
                    "proLogradouro" => "Rua - " . uniqid(rand()),
                    "proCidade"     => "Cidade - " . uniqid(rand()),
                    "proEstado"     => uniqid(rand()),
            ],
        // dependencia declarada como campo json binary no registro pessoa
            "proEnderecoJson"     => [
                    "proLogradouro" => "Rua - " . uniqid(rand()),
                    "proCidade"     => "Cidade - " . uniqid(rand()),
                    "proEstado"     => uniqid(rand()),
            ],
            "proEmprego"          => [
                    "proID"    => 44,
                    "proCargo" => "Desenvolvedor de Sistemas Aquaticos",
            ],
    ]);

    $record = $Pessoa->create();
    if (empty($record)) {
        die('erro ao criar registro');
    }

    var_dump($record->toArray());

} catch (TExceptionAbstract $ex) {
    echo $ex->toJson();
}
