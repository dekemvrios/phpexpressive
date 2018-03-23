<?php

use Solis\Expressive\Test\Fixtures\IntegrationTest\Pessoa;
use Solis\Expressive\Exception;

class DeleteIntegrationTest extends AbstractIntegrationTest
{

    public function testCanDeleteLastRecord()
    {
        $this->createRecord();

        $last = Pessoa::make([])->last();
        $this->assertEquals(true, $last->delete());
    }
}
