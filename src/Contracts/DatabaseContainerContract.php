<?php

namespace Solis\Expressive\Contracts;

use Solis\Expressive\Schema\Contracts\SchemaContract;
use Solis\Breaker\TException;

/**
 * Interface DatabaseCotainerContract
 *
 * @package Solis\Expressive\Contracts
 */
interface DatabaseContainerContract
{

    /**
     * @param ExpressiveContract $model
     * @param array              $arguments
     * @param array              $options
     *
     * @return ExpressiveContract[]boolean
     *
     * @throws TException
     */
    public function select(
        ExpressiveContract $model,
        array $arguments,
        array $options = []
    );

    /**
     * @param ExpressiveContract $model
     *
     * @param boolean            $dependencies
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException
     */
    public function search(ExpressiveContract $model, $dependencies = true);

    /**
     * @param ExpressiveContract $model
     *
     * @return boolean
     *
     * @throws TException
     */
    public function delete(ExpressiveContract $model);

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException
     */
    public function create(ExpressiveContract $model);

    /**
     * @param ExpressiveContract $model
     * @param array              $arguments
     *
     * @return int
     *
     * @throws TException
     */
    public function count(
        ExpressiveContract $model,
        array $arguments = []
    );

    /**
     * @param ExpressiveContract $model
     * @param boolean            $dependencies
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function last(ExpressiveContract $model, $dependencies = true);

    /**
     * @param ExpressiveContract $model
     *
     * @return boolean
     *
     * @throws TException
     */
    public function update(ExpressiveContract $model);

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function patch(ExpressiveContract $model);

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract|boolean
     */
    public function replicate(ExpressiveContract $model);
}
