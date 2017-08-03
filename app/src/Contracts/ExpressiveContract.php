<?php

namespace Solis\Expressive\Contracts;

use Solis\Expressive\Schema\Contracts\SchemaContract;
use Solis\Breaker\TException;

/**
 * Classes ExpressiveContract
 *
 * @package Solis\Expressive\Contracts
 */
interface ExpressiveContract
{
    /**
     * @return SchemaContract
     */
    public function getSchema();

    /**
     * @param SchemaContract $schema
     */
    public function setSchema(SchemaContract $schema);

    /**
     * @param $table
     */
    public function setTable($table);

    /**
     * @return string
     */
    public function getTable();

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
    );

    /**
     * @param boolean $dependencies
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException
     */
    public function search($dependencies = true);

    /**
     * @return boolean
     *
     * @throws TException
     */
    public function delete();

    /**
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function create();

    /**
     * @param array $arguments
     *
     * @return int
     *
     * @throws TException
     */
    public function count(array $arguments = []);

    /**
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function last();

    /**
     * @return boolean
     *
     * @throws TException
     */
    public function update();

    /**
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function patch();

    /**
     * @return string
     */
    public function getUniqid();

    /**
     * @param string $uniqid
     */
    public function setUniqid($uniqid);
}