<?php

namespace Solis\Expressive\Abstractions;

use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Expressive\Contracts\DatabaseContainerContract;
use Solis\Expressive\Schema\Contracts\SchemaContract;
use Solis\Expressive\Schema\Schema;
use Solis\Breaker\TException;

/**
 * Classes ExpressiveAbstract
 *
 * @package Solis\Expressive\Abstractions
 */
abstract class ExpressiveAbstract implements ExpressiveContract
{

    /**
     * @var string
     */
    protected $uniqid;

    /**
     * @var SchemaContract;
     */
    protected $concretSchema;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var DatabaseContainerContract
     */
    protected $databaseContainer;

    /**
     * ExpressiveAbstract constructor.
     *
     * @param string         $file
     * @param string         $table
     * @param SchemaContract $schema
     */
    protected function __construct(
        $file,
        $table,
        $schema
    ) {
        $this->table = $table;
        $this->setUniqid(uniqid(rand()));
        $this->concretSchema = $schema;
    }

    /**
     * __call
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     * @throws TException
     *
     */
    public function __call(
        $name,
        $arguments
    ) {
        if (preg_match(
            '/findBy_/',
            $name
        )) {
            $arguments = [
                "column" => preg_replace(
                    '/findBy_/',
                    '',
                    $name
                ),
                "value" => is_array($arguments) ? $arguments[0] : $arguments
            ];

            return $this->getDatabaseContainer()->select(
                [$arguments],
                [],
                $this
            );
        }

        throw new TException(
            __CLASS__,
            __METHOD__,
            "invalid method call at " . get_class($this),
            '400'
        );
    }

    /**
     * @param $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return SchemaContract
     */
    public function getSchema()
    {
        return $this->concretSchema;
    }

    /**
     * @param SchemaContract $concretSchema
     */
    public function setSchema(SchemaContract $concretSchema)
    {
        $this->concretSchema = $concretSchema;
    }

    /**
     * @return string
     */
    public function getUniqid()
    {
        return $this->uniqid;
    }

    /**
     * @param string $uniqid
     */
    public function setUniqid($uniqid)
    {
        $this->uniqid = $uniqid;
    }

    /**
     * @return DatabaseContainerContract
     */
    protected function getDatabaseContainer()
    {
        return $this->databaseContainer;
    }

    /**
     * @param DatabaseContainerContract $databaseContainer
     */
    protected function setDatabaseContainer($databaseContainer)
    {
        $this->databaseContainer = $databaseContainer;
    }

    /**
     * @param array $arguments
     * @param array $options
     *
     * @return ExpressiveContract[]|ExpressiveContract|boolean
     *
     * @throws TException
     */
    public function select(
        array $arguments,
        array $options = []
    ) {
        return $this->getDatabaseContainer()->select(
            $arguments,
            $options,
            $this
        );
    }

    /**
     * @param boolean $dependencies
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException
     */
    public function search($dependencies = true)
    {
        return $this->getDatabaseContainer()->search($this, $dependencies);
    }

    /**
     * @return boolean
     *
     * @throws TException
     */
    public function delete()
    {
        return $this->getDatabaseContainer()->delete($this);
    }

    /**
     * @return ExpressiveContract|boolean
     *
     * @throws TException
     */
    public function create()
    {
        return $this->getDatabaseContainer()->create($this);
    }

    /**
     * @param array $arguments
     *
     * @return int
     *
     * @throws TException
     */
    public function count(array $arguments = [])
    {
        return $this->getDatabaseContainer()->count(
            !empty($arguments) ? $arguments : [],
            $this
        );
    }

    /**
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function last()
    {
        return $this->getDatabaseContainer()->last($this);
    }

    /**
     * @return boolean
     *
     * @throws TException
     */
    public function update()
    {
        return $this->getDatabaseContainer()->update($this);
    }

    /**
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function patch()
    {
        return $this->getDatabaseContainer()->patch($this);
    }
}
