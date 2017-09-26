<?php

namespace Solis\Expressive\Classes\Illuminate;

use Solis\Expressive\Classes\Illuminate\Replicate\ReplicateBuilder;
use Solis\Expressive\Classes\Illuminate\Select\SelectBuilder;
use Solis\Expressive\Classes\Illuminate\Insert\InsertBuilder;
use Solis\Expressive\Classes\Illuminate\Delete\DeleteBuilder;
use Solis\Expressive\Classes\Illuminate\Update\UpdateBuilder;
use Solis\Expressive\Classes\Illuminate\Patch\PatchBuilder;
use Solis\Expressive\Contracts\DatabaseContainerContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Breaker\TException;

/**
 * Class Wrapper
 *
 * @package Solis\Expressive\Classes\Illuminate
 */
class Wrapper implements DatabaseContainerContract
{

    /**
     * @var SelectBuilder
     */
    protected $selectBuilder;

    /**
     * @var InsertBuilder
     */
    protected $insertBuilder;

    /**
     * @var DeleteBuilder
     */
    protected $deleteBuilder;

    /**
     * @var UpdateBuilder
     */
    protected $updateBuilder;

    /**
     * @var PatchBuilder
     */
    protected $patchBuilder;

    /**
     * @var ReplicateBuilder
     */
    protected $replicateBuilder;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->setReplicateBuilder(new ReplicateBuilder());
        $this->setSelectBuilder(new SelectBuilder());
        $this->setInsertBuilder(new InsertBuilder());
        $this->setDeleteBuilder(new DeleteBuilder());
        $this->setUpdateBuilder(new UpdateBuilder());
        $this->setPatchBuilder(new PatchBuilder());
    }

    /**
     * @return static
     */
    public static function make()
    {
        return new static();
    }

    /**
     * @return SelectBuilder
     */
    public function getSelectBuilder()
    {
        return $this->selectBuilder;
    }

    /**
     * @param SelectBuilder $selectBuilder
     */
    public function setSelectBuilder($selectBuilder)
    {
        $this->selectBuilder = $selectBuilder;
    }

    /**
     * @return InsertBuilder
     */
    public function getInsertBuilder()
    {
        return $this->insertBuilder;
    }

    /**
     * @param InsertBuilder $insertBuilder
     */
    public function setInsertBuilder($insertBuilder)
    {
        $this->insertBuilder = $insertBuilder;
    }

    /**
     * @return DeleteBuilder
     */
    public function getDeleteBuilder()
    {
        return $this->deleteBuilder;
    }

    /**
     * @param DeleteBuilder $deleteBuilder
     */
    public function setDeleteBuilder($deleteBuilder)
    {
        $this->deleteBuilder = $deleteBuilder;
    }

    /**
     * @return UpdateBuilder
     */
    public function getUpdateBuilder()
    {
        return $this->updateBuilder;
    }

    /**
     * @param UpdateBuilder $updateBuilder
     */
    public function setUpdateBuilder($updateBuilder)
    {
        $this->updateBuilder = $updateBuilder;
    }

    /**
     * @return PatchBuilder
     */
    public function getPatchBuilder()
    {
        return $this->patchBuilder;
    }

    /**
     * @param PatchBuilder $patchBuilder
     */
    public function setPatchBuilder($patchBuilder)
    {
        $this->patchBuilder = $patchBuilder;
    }

    /**
     * @return ReplicateBuilder
     */
    public function getReplicateBuilder()
    {
        return $this->replicateBuilder;
    }

    /**
     * @param ReplicateBuilder $replicateBuilder
     */
    public function setReplicateBuilder($replicateBuilder)
    {
        $this->replicateBuilder = $replicateBuilder;
    }

    /**
     * @param ExpressiveContract $model
     * @param array              $arguments
     * @param array              $options
     *
     * @return array|boolean
     *
     * @throws TException
     */
    public function select(
        ExpressiveContract $model,
        array $arguments,
        array $options = []
    ) {
        return $this->getSelectBuilder()->select(
            $model,
            $arguments,
            $options
        );
    }

    /**
     * @param ExpressiveContract $model
     *
     * @param boolean            $dependencies
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException
     */
    public function search(ExpressiveContract $model, $dependencies = true)
    {
        return $this->getSelectBuilder()->search($model, $dependencies);
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return boolean
     *
     * @throws TException;
     */
    public function delete(ExpressiveContract $model)
    {
        return boolval($this->getDeleteBuilder()->delete($model));
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException;
     */
    public function create(ExpressiveContract $model)
    {
        return $this->getInsertBuilder()->create($model);
    }

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
    ) {
        return $this->getSelectBuilder()->count(
            $model,
            $arguments
        );
    }

    /**
     * @param ExpressiveContract $model
     * @param boolean            $dependencies
     *
     * @return ExpressiveContract
     */
    public function last(ExpressiveContract $model, $dependencies = true)
    {
        return $this->getSelectBuilder()->last($model, $dependencies);
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return boolean
     */
    public function update(ExpressiveContract $model)
    {
        return $this->getUpdateBuilder()->update($model);
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return boolean
     */
    public function patch(ExpressiveContract $model)
    {
        return $this->getPatchBuilder()->patch($model);
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract|boolean
     */
    public function replicate(ExpressiveContract $model)
    {
        return $this->getReplicateBuilder()->replicate($model);
    }

    /**
     * fetchStdClassToExpressiveModel
     *
     * @param \stdClass          $stdClass
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     */
    public static function fetchStdClassToExpressiveModel(
        \stdClass $stdClass,
        ExpressiveContract $model
    ) {
        $arrayClass = [$stdClass][0];
        $class = get_class($model);
        $instance = new $class();
        foreach ($arrayClass as $property => $value) {
            if (property_exists(
                $instance,
                $property
            )) {
                $instance->{$property} = $value;
            }
        }

        return $instance;
    }

    /**
     * fetchStdClassToExpressiveNewModel
     *
     * @param \stdClass $stdClass
     * @param string    $class
     *
     * @return ExpressiveContract
     */
    public static function fetchStdClassToExpressiveNewModel(
        $stdClass,
        $class
    ) {
        $arrayClass = [$stdClass][0];
        $instance = new $class();
        foreach ($arrayClass as $property => $value) {
            if (property_exists(
                $instance,
                $property
            )) {
                $instance->{$property} = $value;
            }
        }

        return $instance;
    }
}
