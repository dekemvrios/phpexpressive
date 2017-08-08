<?php

require_once '../../../vendor/autoload.php';;

use Sample\Produto\Classes\Produto;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $last = Produto::make(
        [
            'empcodigo' => 264,
        ]
    )->last() or die('record not found');

    $instance = Produto::make(
        [
            'procodigo' => $last->procodigo,
            'empcodigo' => 264,
        ]
    )->search();
    if (!empty($instance)) {
        var_dump(
            $instance->toArray()
        );
    }

} catch (TException $exception) {
    echo $exception->toJson();
}
