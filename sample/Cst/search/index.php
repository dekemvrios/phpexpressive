<?php

require_once '../../../vendor/autoload.php';

use Sample\Cst\Classes\Cst;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $instance = Cst::make([
        'cstcodigo' => 167,
    ]);

    $record = $instance->search();
    var_dump(
        !empty($record) ? $record->toArray() : $record
    );


} catch (TException $exception) {
    echo $exception->toJson();
}
