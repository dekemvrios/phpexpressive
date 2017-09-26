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
     * @param array $arguments
     * @param array $options
     *
     * @return ExpressiveContract[]|boolean
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
     * @param boolean $dependencies
     * 
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function last($dependencies = true);

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
     * @return ExpressiveContract|boolean
     */
    public function replicate();

    /**
     * @return string
     */
    public function getUniqid();

    /**
     * @param string $uniqid
     */
    public function setUniqid($uniqid);
}