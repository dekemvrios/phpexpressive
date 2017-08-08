<?php

require_once '../../../vendor/autoload.php';

use Sample\Produto\Classes\Produto;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $result = (new Produto())->select(
        [
            [
                'column' => 'empcodigo',
                'value'  => 264,
            ],
        ],
        [
            "orderBy"          => [
                'column'    => 'procodigo',
                'direction' => 'asc',
            ],
            "limit"            => [
                "number" => 10,
                "offset" => 5,
            ],
            "withProperties"   => [
                // "empcodigo",
                // "procodigo",
                "prodescricao",
                "cstipicodigo",
            ],
            "withDependencies" => true,
        ]
    );

    if (!empty($result)) {
        if (is_array($result)) {
            foreach ($result as $item) {
                var_dump($item->toArray());
            }
        } else {
            var_dump($result->toArray());
        }
    }

} catch (TException $exception) {
    echo $exception->toJson();
}
