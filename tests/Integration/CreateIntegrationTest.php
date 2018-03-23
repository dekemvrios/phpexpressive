<?php

namespace Solis\Expressive\Test\Integration;

use Solis\Expressive\Test\Fixtures\IntegrationTest\Pessoa;
use Solis\Expressive\Exception;

class CreateIntegrationTest extends AbstractIntegrationTest
{

    public function testCanCreateOneRecord()
    {
        $record = $this->createRecord();

        $this->assertInternalType('int', $record->ID);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateMustThrowExceptionForMissingValue()
    {
        Pessoa::make([])->create();
    }
}
