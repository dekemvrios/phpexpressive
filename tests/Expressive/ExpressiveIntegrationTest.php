<?php

namespace Solis\Expressive\Test\Expressive;

use PHPUnit\Framework\TestCase;

use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Expressive\Test\Fixtures\Pessoa\Repository\Pessoa;

class ExpressiveIntegrationTest extends TestCase
{

    public function setUp()
    {
        Database::boot([
                'driver'   => getenv('DB_DRIVER'),
                'host'     => getenv('DB_HOST'),
                'database' => getenv('DB_NAME'),
                'username' => getenv('DB_USER'),
                'password' => getenv('DB_PASS'),
        ]);
    }

    public function testBasicRecordCreation()
    {
        $Pessoa = Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ]);
        $Record = $Pessoa->create();
        $this->assertInternalType('int', $Record->ID, 'can\'t create one record in database');
    }

    public function testCreateRecordWithOneHasManyDependency()
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

        $enderecos = $Last->endereco;
        $this->assertInternalType('array', $enderecos, 'can\'t create record with one hasMany dependency');
    }

    public function testCreateRecordWithMultiHasManyDependencies()
    {
        $numberOfDependencies = rand(1, 4);
        $dependencies         = [];

        for ($i = 0; $i < $numberOfDependencies; $i++) {
            $dependencies[] = [
                    "proLogradouro" => "Rua - " . uniqid(rand()),
                    "proCidade"     => "Cidade - " . uniqid(rand()),
                    "proEstado"     => uniqid(rand()),
            ];
        }

        Pessoa::make([
                "proNome"     => 'Fulano - ' . uniqid(rand()),
                "proEndereco" => $dependencies,
        ])->create();

        $Last = Pessoa::make()->last();

        $enderecos = $Last->endereco;
        $this->assertCount($numberOfDependencies, $enderecos, 'can\'t create record with multi hasMany dependency');
    }

    public function testCanRetrieveLastCreatedRecord()
    {
        $Pessoa = Pessoa::make()->last();
        $this->assertInternalType('int', $Pessoa->ID, 'can\'t last created record');
    }

    public function testCanDeleteLastRecord()
    {
        $Pessoa = Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ]);
        $Record = $Pessoa->create();
        $this->assertEquals(true, $Record->delete(), 'can\'t delete last created record');
    }

    public function testCanRetrieveAllRecords()
    {
        Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();
        $Pessoas = (new Pessoa())->select([], []);
        $this->assertInternalType('array', $Pessoas, 'can\'t retrieve records from database with select method');
    }

    public function testCanCountAllRecords()
    {
        Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();
        $count = (new Pessoa())->count();
        $this->assertGreaterThan(0, $count, 'can\'t return count of records from database');
    }

    public function testCanRetrieveRecordWithSearchMethod()
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
                                [
                                        'column' => 'ID',
                                        'value'  => $Record->ID,
                                ],
                        ]
                );
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

    public function testNotSuppliedFieldsShouldBeNullWhenPatchUpdate()
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

        $Last = Pessoa::make()->last();

        $documentoLast = $Last->inscricaoFederal;
        $nomeLast      = $Last->nome;

        $this->assertInternalType('null', $documentoLast, 'a not supplied field must be null in a patched record');
        $this->assertEquals($nome, $nomeLast, 'a supplied field cannot be null in a patched record');
    }
}