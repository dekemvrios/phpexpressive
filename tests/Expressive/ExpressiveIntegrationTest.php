<?php

namespace Solis\Expressive\Test\Expressive;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase;
use Solis\Expressive\Test\Fixtures\Pessoa\Repository\Pessoa;

class ExpressiveIntegrationTest extends TestCase
{

    public function setUp()
    {
        (new DatabaseBuilder())->up();
    }

    public function tearDown()
    {
        (new DatabaseBuilder())->down();
    }

    public function testBasicRecordCreation()
    {
        $Pessoa = Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ]);
        $Record = $Pessoa->create();
        $this->assertInternalType('int', $Record->ID, 'can\'t create one record in database');
    }

    public function testCanRetrieveLastCreatedRecord()
    {
        Pessoa::make([
                "proNome" => 'Fulano - ' . uniqid(rand()),
        ])->create();
        $Last = Pessoa::make()->last();
        $this->assertInternalType('int', $Last->ID, 'can\'t last created record');
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

        $Last          = Pessoa::make()->last();
        $documentoLast = $Last->inscricaoFederal;
        $nomeLast      = $Last->nome;

        $this->assertInternalType('null', $documentoLast, 'a not supplied field must be null in a patched record');
        $this->assertEquals($nome, $nomeLast, 'a supplied field cannot be null in a patched record');
    }
}

class DatabaseBuilder {

    /**
     * Set up the Eloquent Connection to the database
     */
    public function up()
    {
        $db = new DB;
        $db->addConnection([
                'driver'   => 'sqlite',
                'database' => ':memory:',
        ]);
        $db->bootEloquent();

        $db->setAsGlobal();
        $this->createDatabaseSchemas();
    }

    /**
     * Destroy unit test database schemas
     */
    public function down()
    {
        $this->dropDatabaseSchemas();
    }

    /**
     * Create all the schemas used by this test case
     */
    protected function createDatabaseSchemas()
    {
        $this->createRelationPessoa();
        $this->createRelationEndereco();
    }

    /**
     * Ddrop all schemas used by this test case
     */
    protected function dropDatabaseSchemas()
    {
        $this->dropRelationEndereco();
        $this->dropRelationPessoa();
    }

    /**
     * Use illuminate schema builder to create the table pessoa
     */
    protected function createRelationPessoa()
    {
        $this->schema()->create('pessoa', function ($table) {
            $table->increments('ID');
            $table->string('nome')->nullable();
            $table->string('inscricaoFederal')->nullable();
            $table->integer('situacao')->nullable();
            $table->text('enderecoJson')->default(json_encode([]))->nullable();
        });
    }

    /**
     * Use illuminate schema builder to drop the table pessoa
     */
    protected function dropRelationPessoa()
    {
        $this->schema()->drop('pessoa');
    }

    /**
     * Use illuminate schema builder to create the table endereco
     */
    protected function createRelationEndereco()
    {
        $this->schema()->create('endereco', function ($table) {
            $table->increments('ID');
            $table->integer('pessoaID');
            $table->string('logradouro');
            $table->integer('numero');
            $table->string('bairro');
            $table->string('cep');
            $table->string('cidade');
            $table->string('estado');
        });
    }

    /**
     * Use illuminate schema builder to drop the table endereco
     */
    protected function dropRelationEndereco()
    {
        $this->schema()->drop('endereco');
    }

    /**
     * Get a database connection instance.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    protected function connection($connection = 'default')
    {
        return Eloquent::getConnectionResolver()->connection($connection);
    }

    /**
     * Get a schema builder instance.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function schema($connection = 'default')
    {
        return $this->connection($connection)->getSchemaBuilder();
    }
}
