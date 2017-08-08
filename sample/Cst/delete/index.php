<?php

require_once '../../../vendor/autoload.php';

use Sample\Cst\Classes\Cst;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    var_dump(
        (new Cst())
            ->last()
            ->delete()
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
