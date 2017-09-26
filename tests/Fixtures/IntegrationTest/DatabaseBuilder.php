<?php

namespace Solis\Expressive\Test\Fixtures\IntegrationTest;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Capsule\Manager as DB;

class DatabaseBuilder
{

    /**
     * Set up the Eloquent Connection to the database
     */
    public function build()
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
            $table->string('cidade');
            $table->string('estado');
            $table->integer('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cep')->nullable();
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
