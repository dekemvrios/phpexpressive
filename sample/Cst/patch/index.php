<?php

require_once '../../../vendor/autoload.php';

use Sample\Cst\Classes\Cst;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $instance = Cst::make([])->last() or die('not found record for class');

    $instance->csttipo = 23;

    $instance->cstcst = 'UPDATE TEST [' . uniqid(rand()) . ']';

    var_dump(
        $instance->patch()
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
