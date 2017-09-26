<?php

namespace Solis\Expressive\Test\Expressive;

use PHPUnit\Framework\TestCase;
use Solis\Expressive\Test\Fixtures\IntegrationTest\Pessoa;
use Solis\Expressive\Test\Fixtures\IntegrationTest\DatabaseBuilder as DB;

class ExpressiveIntegrationTest extends TestCase
{

    public function setUp()
    {
        (new DB())->up();
    }

    public function tearDown()
    {
        (new DB())->down();
    }

    public function testCanCreateOneRecord()
    {
        $Pessoa = Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ]);
        $Record = $Pessoa->create();
        $this->assertInternalType('int', $Record->ID, 'can\'t create one record in database');
    }

    public function testCanRetrieveLastRecord()
    {
        Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();
        $Last = Pessoa::make()->last();
        $this->assertInternalType('int', $Last->ID, 'can\'t last created record');
    }

    public function testCanCreateOneRecordWithSingleHasMany()
    {
        Pessoa::make([
                "proNome"     => 'Fulano - ' . uniqid(rand()),
                "proEndereco" => [
                        "proLogradouro" => "Rua - " . uniqid(rand()),
                        "proCidade"     => "Cidade - " . uniqid(rand()),
                        "proEstado"     => uniqid(rand()),
                ],
        ])->create();

        $Last = Pessoa::make()->last();

        $endereco = $Last->endereco;
        $this->assertNotInternalType('null', $endereco, 'can\'t create dependency associated in hasMany');
    }

    public function testCanCreateOneRecordWithMultipleHasMany()
    {
        $dependencies = rand(1, 5);

        $hasMany = [];
        for ($i = 0; $i < $dependencies; $i++) {
            $hasMany[] = [
                    "proLogradouro" => "Rua - " . uniqid(rand()),
                    "proCidade"     => "Cidade - " . uniqid(rand()),
                    "proEstado"     => uniqid(rand()),
            ];
        }

        Pessoa::make([
                "proNome"     => 'Fulano - ' . uniqid(rand()),
                "proEndereco" => $hasMany,
        ])->create();

        $Last = Pessoa::make()->last();

        $endereco = $Last->endereco;
        $this->assertCount($dependencies, $endereco, 'can\'t create multi dependencies associated in hasMany');
    }

    public function testCanDeleteLastRecord()
    {
        $Pessoa = Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ]);
        $Record = $Pessoa->create();
        $this->assertEquals(true, $Record->delete(), 'can\'t delete last created record');
    }

    public function testCanRetrieveRecordsOfRepository()
    {
        Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();
        $Pessoas = (new Pessoa())->select([], []);
        $this->assertInternalType('array', $Pessoas, 'can\'t retrieve records from database with select method');
    }

    public function testCanCountRecordsInRepository()
    {
        Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();
        $count = (new Pessoa())->count();
        $this->assertGreaterThan(0, $count, 'can\'t return count of records from database');
    }

    public function testCanRetrieveWithSearchMethod()
    {
        $Pessoa = Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();
        $Record = Pessoa::make(['ID' => $Pessoa->ID])->search();
        $this->assertEquals(
            $Pessoa->ID,
            $Record->ID,
            'can\'t retrieve last created record in database with search method'
        );
    }

    public function testCanRetrieveRecordWithSelectMethod()
    {
        $Record = Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();
        $select = (new Pessoa())
                ->select([
                        'column' => 'ID',
                        'value'  => $Record->ID,
                ]);
        $this->assertInternalType(
            'array',
            $select,
            'can\'t retrieve last created record in database with select method'
        );
    }

    public function testCanReplicateRecord()
    {
        Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();
        $Last       = Pessoa::make()->last();
        $Replicated = $Last->replicate();
        $this->assertGreaterThan($Last->ID, $Replicated->ID, 'can\'t replicate last database record');
    }

    public function testCanUpdateRecord()
    {
        $proNomeOriginal = 'Fulano - ' . uniqid(rand());

        Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();

        $Update        = Pessoa::make()->last();
        $proNomeUpdate = 'Fulano - ' . uniqid(rand());

        $Update->nome = $proNomeUpdate;
        $Update->update();

        $Last        = Pessoa::make()->last();
        $proNomeLast = $Last->nome;

        $this->assertNotEquals($proNomeOriginal, $proNomeLast, 'can\'t update database record');
    }

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

        $Last     = Pessoa::make()->last();
        $nomeLast = $Last->nome;

        $this->assertNotEquals($original, $nomeLast, 'Patched field is even with original value');
    }

    public function testPatchShouldCleanNotSuppliedValues()
    {
        $nome      = 'Fulano - ' . uniqid(rand());
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

        $Last          = Pessoa::make()->last();
        $documentoLast = $Last->inscricaoFederal;

        $this->assertInternalType('null', $documentoLast, 'a not supplied field must be null in a patched record');
    }
}
