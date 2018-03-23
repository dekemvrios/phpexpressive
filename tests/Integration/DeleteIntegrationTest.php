<?php

namespace Solis\Expressive\Test\Integration;

use Solis\Expressive\Test\Fixtures\IntegrationTest\Pessoa;

class DeleteIntegrationTest extends AbstractIntegrationTest
{

    public function testCanDeleteLastRecord()
    {
        $this->createRecord();

        $last = Pessoa::make([])->last();
        $this->assertEquals(true, $last->delete());
    }
}
