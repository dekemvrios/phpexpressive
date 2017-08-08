<?php

require_once '../../../vendor/autoload.php';

use Sample\Produto\Classes\Produto;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $instance = Produto::make(
        [
            'empcodigo' => 264,
            'procodigo' => 67,
        ]
    )->search() or die ('not found record for class');

    $instance->prodescricao = 'EMP 264 UPDATE TEST [ ' . Date('Y-m-d H:m:s') . '] - ' . uniqid(rand());
    $instance->giccodigo = 2;
    $instance->iCstIpiCodigo = [
        "iCstCodigo" => 167,
    ];

    var_dump(
        $instance->patch()
    );
} catch (TException $exception) {
    echo $exception->toJson();
}
