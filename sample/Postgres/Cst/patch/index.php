<?php

require_once '../../../../vendor/autoload.php';

use Solis\Expressive\Classes\Illuminate\Database;
use Sample\Postgres\Cst\Classes\Cst;
use Solis\Breaker\TException;

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

    $instance = Cst::make([])->last() or die('not found record for class');

    $instance->csttipo = 23;

    $instance->cstcst = 'UPDATE TEST [' . uniqid(rand()) . ']';

    var_dump(
        $instance->patch()
    );

} catch (TException $exception) {
    echo $exception->toJson();
}
