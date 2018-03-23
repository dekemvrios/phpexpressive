<?php

namespace Solis\Expressive\Test\Integration;

use Solis\Expressive\Test\Fixtures\IntegrationTest\Pessoa;
use Solis\Expressive\Exception;

class SelectIntegrationTest extends AbstractIntegrationTest
{

    public function testCanRetrieveAllRecords()
    {
        $this->createRecord(rand(2, 10));
        $records = (new Pessoa())->select([], []);
        $this->assertInternalType('array', $records);
    }

    public function testCanRetrieveWithLast()
    {
        $this->createRecord();

        $last = Pessoa::make()->last();
        $this->assertNotInternalType('null', $last->ID);
    }

    public function testCanCountRecords()
    {
        $this->createRecord();
        $count = (new Pessoa())->count();
        $this->assertGreaterThan(0, $count);
    }

    public function testCanRetrieveWithSearch()
    {
        $record = $this->createRecord();

        $search = Pessoa::make(['ID' => $record->ID])->search();
        $this->assertEquals($record->ID, $search->ID);
    }

    public function testCanRetrieveOneWithSelect()
    {
        $record = $this->createRecord();
        $select = (new Pessoa())
            ->select([
                'column' => 'ID',
                'value'  => $record->ID,
            ]);
        $this->assertCount(1, $select);
    }
}
