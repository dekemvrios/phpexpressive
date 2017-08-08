<?php

require_once '../../../vendor/autoload.php';

use Sample\Produto\Classes\Produto;

try {

    require '../../Database/config.php';

    $last = Produto::make(
        [
            'empcodigo' => 264
        ]
    )->last();

    if (!empty($last)) {
        $Produto = Produto::make(
            [
                "procodigo"     => $last->procodigo + 1,
                "empcodigo"     => 264,
                "iGicCodigo"    => 1,
                "iGpcCodigo"    => 1,
                "prodescricao"  => 'test [ ' . uniqid(rand()) . ' ]',
                "iCstIpiCodigo" => [
                    "cstcodigo"                 => 356,
                    "csttipo"                   => 2,
                    "cstcst"                    => "AA",
                    "cstdescricao"              => 'Eloquent Test [ ' . uniqid(rand()) . ' ]',
                    "cstsubstituicaotributaria" => 0
                ]
            ]
        )->create();

        var_dump(
            !empty($Produto) ? $Produto->toArray() : $Produto
        );
    }

} catch (\Exception $exception) {
    var_dump($exception);
}