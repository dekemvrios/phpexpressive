<?php

namespace Solis\Expressive\Test\Integration;

use Solis\Expressive\Test\Fixtures\IntegrationTest\Pessoa;

class UpdateIntegrationTest extends AbstractIntegrationTest
{
    public function testCanUpdateRecord()
    {
        $proNomeOriginal = 'Fulano - ' . uniqid(rand());

        Pessoa::make([
            "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();

        $Update = Pessoa::make()->last();
        $proNomeUpdate = 'Fulano - ' . uniqid(rand());

        $Update->nome = $proNomeUpdate;
        $Update->update();

        $Last = Pessoa::make()->last();
        $proNomeLast = $Last->nome;

        $this->assertNotEquals($proNomeOriginal, $proNomeLast);
    }
}
