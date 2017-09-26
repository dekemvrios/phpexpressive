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
     * __call
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     * @throws TException
     */
    public function __call(
        $name,
        $arguments
    ) {
        if (preg_match(
            '/findBy_/',
            $name
        )
        ) {
            $arguments = [
                "column" => preg_replace(
                    '/findBy_/',
                    '',
                    $name
                ),
                "value" => is_array($arguments) ? $arguments[0] : $arguments
            ];

            return $this->getDatabaseContainer()->select(
                $this
                    [$arguments],
                []
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
            $this,
            !empty($arguments) ? $arguments : []
        );
    }

    /**
     * @param boolean $dependencies
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function last($dependencies = true)
    {
        return $this->getDatabaseContainer()->last($this, $dependencies);
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

    /**
     * @return ExpressiveContract|boolean
     */
    public function replicate()
    {
        return $this->getDatabaseContainer()->replicate($this);
    }
}
