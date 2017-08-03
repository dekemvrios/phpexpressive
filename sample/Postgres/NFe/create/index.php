<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\NFe\Classes\NFe;
use Solis\Breaker\TException;

try {

    Database::boot(
        [
            'driver'   => 'pgsql',
            'host'     => 'database',
            'database' => 'empresarial',
            'username' => 'postgres',
            'password' => '4hvU1kbzGe',
        ]
    );

    $iNFeSequencia = 300;

    $produtos = [];
    for ($i = 1; $i <= 10; $i++) {
        $produtos[] = [
            "iNFeSequencia"  => $iNFeSequencia,
            "iEmpCodigo"     => 263,
            "iItnSequencia"  => $i,
            "sItnDescricao"  => "qualquer descricao que houver",
            "iItnQuantidade" => 2,
            "iItnValorUnit"  => 1,
            "iItnValorTotal" => 10,
            "iItnNcmDesc"    => "aaa",
            "iItnCstDesc"    => "bbb",
            "iItnUnDesc"     => "ccc"
        ];
    }

    $instance = NFe::make(
        [
            "iNFeSequencia"    => $iNFeSequencia,
            "iEmpCodigo"       => 263,
            "iNFeSerie"        => 3,
            "iNFeNumero"       => 4,
            "iFilCodigo"       => 1,
            "sNFeModelo"       => '55',
            "iNFeFinalidade"   => 1,
            "iNFeSituacao"     => 1,
            "iNFeFormaEmissao" => 1,
            "sNFeDataEmissao"  => Date('Y-m-d'),
            "sNFeHoraEmissao"  => Date('H:m:s'),
            "iNFeTipoDoc"      => 1,
            "iNFeTipoPag"      => 1,
            "iNFeConFinal"     => 1,
            "iNFeIndPres"      => 1,
            "iNFeDestOp"       => 1,
            "iNFeTipoAmb"      => 1,
            "iNFeNatCod"       => 1,
            "iNFeCidCod"       => 1,
            "iNFePesCod"       => 1,
            "iNFeValorTot"     => 1,
            "iNFeProdValorTot" => 1,
            "iNFeValorTotDesc" => 1,
            "iNFeModFrete"     => 1,
            "aProdutos"        => $produtos
        ]
    );

    var_dump(
        $instance->create()
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
