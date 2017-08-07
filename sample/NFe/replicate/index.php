<?php

require_once '../../../vendor/autoload.php';

use Sample\NFe\Classes\NFe;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $instance = NFe::make([
        'iEmpCodigo'    => 263,
        'iNFeSequencia' => 300,
    ])->search();

    if (!empty($instance)) {
        $record = $instance->replicate();

        var_dump($record->toArray());
    }

} catch (TException $exception) {
    echo $exception->toJson();
}
