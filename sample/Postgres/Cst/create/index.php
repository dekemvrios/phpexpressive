<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\Cst\Classes\Cst;

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

    $Cst = Cst::make(
        [
            "iCsttipo"      => 2,
            "sCstCst"       => "AA",
            "sCstDescricao" => 'Eloquent Test [ ' . uniqid(rand()) . ' ]',
            "iCstSt"        => 1
        ]
    );

    var_dump(
        $Cst->create()
    );

} catch (\Exception $exception) {
    var_dump($exception);
}
