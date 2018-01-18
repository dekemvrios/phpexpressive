<?php

namespace Solis\Expressive\Contracts;

use Solis\Breaker\Abstractions\TExceptionAbstract;

/**
 * Interface DatabaseContainerContract
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
     * @throws TExceptionAbstract
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
     * @throws TExceptionAbstract
     */
    public function search(ExpressiveContract $model, $dependencies = true);

    /**
     * @param ExpressiveContract $model
     *
     * @return boolean
     *
     * @throws TExceptionAbstract
     */
    public function delete(ExpressiveContract $model);

    /**
     * @param ExpressiveContract $model
     *
     * @return boolean
     *
     * @throws TExceptionAbstract
     */
    public function disable(ExpressiveContract $model);

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TExceptionAbstract
     */
    public function create(ExpressiveContract $model);

    /**
     * @param ExpressiveContract $model
     * @param array              $arguments
     *
     * @return int
     *
     * @throws TExceptionAbstract
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
     * @throws TExceptionAbstract
     */
    public function last(ExpressiveContract $model, $dependencies = true);

    /**
     * @param ExpressiveContract $model
     *
     * @return boolean
     *
     * @throws TExceptionAbstract
     */
    public function update(ExpressiveContract $model);

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function patch(ExpressiveContract $model);

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract|boolean
     */
    public function replicate(ExpressiveContract $model);
}
