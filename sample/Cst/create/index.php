<?php

require_once '../../../vendor/autoload.php';

use Sample\Cst\Classes\Cst;

try {

    require '../../Database/config.php';

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
