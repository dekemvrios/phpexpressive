<?php

namespace Solis\Expressive\Test\Integration;

use Solis\Expressive\Test\Fixtures\IntegrationTest\Pessoa;

class PatchIntegrationTest extends AbstractIntegrationTest
{

    public function testCanPatchRecord()
    {
        $original = 'Fulano - ' . uniqid(rand());
        Pessoa::make([
            "proNome" => $original,
        ])->create();

        $Last = Pessoa::make()->last();

        $updated = 'Fulano - ' . uniqid(rand());
        Pessoa::make([
            "proID"   => $Last->ID,
            "proNome" => $updated,
        ])->patch();

        $Last = Pessoa::make()->last();
        $nomeLast = $Last->nome;

        $this->assertNotEquals($original, $nomeLast);
    }

    public function testPatchShouldCleanNotSuppliedValues()
    {
        $nome = 'Fulano - ' . uniqid(rand());
        $documento = rand(11111111111, 99999999999);

        Pessoa::make([
            "proNome"             => $nome,
            "proInscricaoFederal" => "{$documento}",
        ])->create();

        $Last = Pessoa::make()->last();

        Pessoa::make([
            "proID"   => $Last->ID,
            "proNome" => $Last->nome,
        ])->patch();

        $Last = Pessoa::make()->last();
        $documentoLast = $Last->inscricaoFederal;

        $this->assertInternalType('null', $documentoLast);
    }
}
