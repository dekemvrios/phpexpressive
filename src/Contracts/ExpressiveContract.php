<?php

namespace Solis\Expressive\Contracts;

use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Expressive\Schema\Contracts\SchemaContract;

/**
 * Interface ExpressiveContract
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
     * @throws TExceptionAbstract
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
     * @throws TExceptionAbstract
     */
    public function search($dependencies = true);

    /**
     * @return boolean
     *
     * @throws TExceptionAbstract
     */
    public function delete();

    /**
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function create();

    /**
     * @param array $arguments
     *
     * @return int
     *
     * @throws TExceptionAbstract
     */
    public function count(array $arguments = []);

    /**
     * @param boolean $dependencies
     *
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function last($dependencies = true);

    /**
     * @return boolean
     *
     * @throws TExceptionAbstract
     */
    public function update();

    /**
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
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
