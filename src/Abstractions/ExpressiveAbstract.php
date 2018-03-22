<?php

namespace Solis\Expressive\Abstractions;

use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Expressive\Contracts\DatabaseContainerContract;
use Solis\Breaker\Abstractions\TExceptionAbstract;

/**
 * Class ExpressiveAbstract
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
     * @var DatabaseContainerContract
     */
    protected $databaseContainer;

    /**
     * ExpressiveAbstract constructor.
     */
    protected function __construct()
    {
        $this->setUniqid(uniqid(rand()));
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
     * @throws TExceptionAbstract
     */
    public function select(
        array $arguments,
        array $options = []
    ) {
        return $this->getDatabaseContainer()->select(
            $this,
            $arguments,
            $options
        );
    }

    /**
     * @param boolean $dependencies
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TExceptionAbstract
     */
    public function search($dependencies = true)
    {
        return $this->getDatabaseContainer()->search($this, $dependencies);
    }

    /**
     * @return boolean
     *
     * @throws TExceptionAbstract
     */
    public function delete()
    {
        return $this->getDatabaseContainer()->delete($this);
    }

    /**
     * @return boolean
     *
     * @throws TExceptionAbstract
     */
    public function disable()
    {
        return $this->getDatabaseContainer()->disable($this);
    }

    /**
     * @return ExpressiveContract|boolean
     *
     * @throws TExceptionAbstract
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
     * @throws TExceptionAbstract
     */
    public function count(array $arguments = [])
    {
        return $this->getDatabaseContainer()->count(
            $this,
            !empty($arguments) ? $arguments : []
        );
    }

    /**
     * @param boolean $dependencies
     *
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function last($dependencies = true)
    {
        return $this->getDatabaseContainer()->last($this, $dependencies);
    }

    /**
     * @return boolean
     *
     * @throws TExceptionAbstract
     */
    public function update()
    {
        return $this->getDatabaseContainer()->update($this);
    }

    /**
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function patch()
    {
        return $this->getDatabaseContainer()->patch($this);
    }

    /**
     * @param int $times
     *
     * @return ExpressiveContract|boolean
     */
    public function replicate($times = 1)
    {
        return $this->getDatabaseContainer()->replicate($this, $times);
    }
}
