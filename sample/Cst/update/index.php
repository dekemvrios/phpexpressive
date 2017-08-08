<?php

require_once '../../../vendor/autoload.php';

use Sample\Cst\Classes\Cst;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $instance = Cst::make([])->last();

    if (!empty($instance)) {
        $instance->csttipo = 23;
        $instance->cstcst = 'UPDATE TEST [' . uniqid(rand(), true) . ']';

        if(!empty($instance->update())){
            var_dump(
                Cst::make([])->last()
            );
        }
    }

} catch (TException $exception) {
    echo $exception->toJson();
}
