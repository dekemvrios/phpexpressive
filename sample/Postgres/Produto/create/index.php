<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\Produto\Classes\Produto;

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
                    "cstcodigo"                 => 167,
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