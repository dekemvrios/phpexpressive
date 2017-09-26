<?php

namespace Solis\Expressive\Abstractions;

use Solis\Expressive\Contracts\DatabaseContainerContract;
use Solis\Expressive\Schema\Contracts\SchemaContract;

/**
 * Class DatabaseContainerAbstract
 *
 * @package Solis\Expressive\Abstractions
 */
abstract class DatabaseContainerAbstract implements DatabaseContainerContract
{

    /**
     * @var SchemaContract
     */
    protected $schema;

    /**
     * @var string
     */
    protected $table;

    /**
     * DatabaseContainerAbstract constructor.
     *
     * @param SchemaContract $schema
     * @param string         $table
     */
    protected function __construct($schema, $table)
    {
        $this->schema = $schema;
        $this->table = $table;
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
        return $this->schema;
    }

    /**
     * @param SchemaContract $schema
     */
    public function setSchema(SchemaContract $schema)
    {
        $this->schema = $schema;
    }
}
