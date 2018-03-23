<?php

namespace Solis\Expressive\Test\Integration;

use Solis\Expressive\Test\Fixtures\IntegrationTest\Pessoa;
use Solis\Expressive\Exception;

class ReplicateIntegrationTest extends AbstractIntegrationTest
{
    public function testCanReplicateRecord()
    {
        $record = $this->createRecord();
        $replicated = $record->replicate();
        $this->assertGreaterThan($record->ID, $replicated->ID);
    }

    public function testCanReplicateAnyNumberOfRecords()
    {
        $number = rand(2, 10);
        $record = $this->createRecord();

        $records = $record->replicate($number);
        $this->assertCount($number, $records);
    }

    public function testReplicatedRecordsMustHaveGreaterIdsThanLast()
    {
        $number = rand(1, 10);
        $last = $this->createRecord();

        $records = $last->replicate($number);
        $records = !is_array($records) ? [$records] : $records;

        $base = $last;
        foreach ($records as $record) {
            $this->assertGreaterThan($base->ID, $record->ID);

            $base = $record;
        }
    }
}
