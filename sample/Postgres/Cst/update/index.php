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
