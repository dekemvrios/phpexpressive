<?php

require_once '../../../vendor/autoload.php';

use Sample\NFe\Classes\NFe;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $last = NFe::make(
        [
            'iEmpCodigo' => 263,
        ]
    )->last() or die('not found last record for NFe');

    var_dump(
        $last
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
