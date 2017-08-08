<?php

require_once '../../../vendor/autoload.php';
;
use Sample\NFe\Classes\NFe;
use Solis\Breaker\TException;

try {

    require '../../Database/config.php';

    $result = (new NFe())->select(
        [
            [
                "column" => "empcodigo",
                "value"  => 264,
            ],
        ], [
            'limit'            => [
                'number' => 1,
                'offset' => 14,
            ],
            'orderBy'          => [
                'column'    => 'nfesequencia',
                'direction' => 'asc',
            ],
            'withDependencies' => 'true',
            'withProperties'   => [
                'nfesequencia',
                'empcodigo',
                'nfenumero',
                'nfeserie',
                'itemnfe',
            ],
        ]
    );

    if (!empty($result)) {
        $result = !is_array($result) ? [$result] : $result;

        foreach ($result as $item) {
            var_dump(
                $item->toArray()
            );
        }
    }

} catch (TException $exception) {
    echo $exception->toJson();
}

