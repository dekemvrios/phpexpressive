<?php

require_once '../../../../../vendor/autoload.php';

use Solis\Expressive\Sample\Pessoa\Repository\Pessoa;
use Solis\Expressive\Classes\Illuminate\Diglett;
use Solis\Breaker\TException;

try {

    require '../../../connection/connection.php';

    $first = getFirst();

    $last = getLast();

    $ini = $first->ID;
    $end = $last->ID;

    $counter = 0;
    for ($i = $ini; $i <= $end; $i++) {
        if ($counter >= 5) {
            break;
        }

        Diglett::enable(1);

        var_dump(
            Pessoa::make([
                'proID' => $i
            ])->search()->toArray(true, false)
        );

        Diglett::disable();

        $counter++;
    }

} catch (TException $exception) {
    echo $exception->toJson();
}


function getFirst()
{
    $first = (new Pessoa())->select([], [
        "orderBy" => [
            "column"    => "ID",
            "direction" => "asc",
        ],
        "limit"   => [
            "number" => 1,
        ],
    ]) or die('nao há registros cadastrados na base de dados');

    return $first[0];

}

function getLast()
{
    $last = (new Pessoa())->last() or die('nao há registros cadastrados na base de dados');

    return $last;
}