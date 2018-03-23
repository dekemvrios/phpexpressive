<?php

use PHPUnit\Framework\TestCase;
use Solis\Expressive\Test\Fixtures\IntegrationTest\DatabaseBuilder as DB;
use Solis\Expressive\Test\Fixtures\IntegrationTest\Pessoa;

abstract class AbstractIntegrationTest extends TestCase
{

    protected static $ready = false;

    public function setUp()
    {
        (new DB())->build();
    }

    public function tearDown()
    {
        (new DB())->down();
    }

    public function createRecord($times = 1)
    {
        $records = [];
        for ($i = 0; $i < $times; $i++) {
            $records[] = Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
            ])->create();
        }

        return count($records) == 1 ? $records[0] : $records;
    }
}
