<?php

namespace Solis\Expressive\Classes\Illuminate;

use Solis\Expressive\Classes\Illuminate\Select\SelectBuilder;
use Solis\Expressive\Classes\Illuminate\Insert\InsertBuilder;
use Solis\Expressive\Classes\Illuminate\Delete\DeleteBuilder;
use Solis\Expressive\Classes\Illuminate\Update\UpdateBuilder;
use Solis\Expressive\Classes\Illuminate\Patch\PatchBuilder;
use Solis\Expressive\Contracts\DatabaseContainerContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\PhpSchema\Contracts\SchemaContract;
use Solis\Breaker\TException;

/**
 * Class Wrapper
 *
 * @package Solis\Expressive\Classes\Illuminate
 */
class Wrapper implements DatabaseContainerContract
{

    /**
     * @var SchemaContract
     */
    protected $schema;

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
     * __construct
     */
    public function __construct()
    {
        $this->setSelectBuilder(new SelectBuilder());
        $this->setInsertBuilder(new InsertBuilder());
        $this->setDeleteBuilder(new DeleteBuilder());
        $this->setUpdateBuilder(new UpdateBuilder());
        $this->setPatchBuilder(new PatchBuilder());
    }

    /**
     * make
     *
     * @param  SchemaContract $schema
     *
     * @return static
     */
    public static function make(
        $schema
    ) {
        $instance = new static();
        $instance->schema = $schema;

        return $instance;
    }

    /**
     * @return SchemaContract
     *
     * @throws TException
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param SchemaContract $schema
     *
     * @throws TException
     */
    public function setSchema(SchemaContract $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param $table
     *
     * @throws TException
     */
    public function setTable($table)
    {
        $this->getSchema()->getDatabase()->setTable($table);
    }

    /**
     * @return string
     *
     * @throws TException
     */
    public function getTable()
    {
        return $this->getSchema()->getDatabase()->getTable();
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
     * @param array              $arguments
     * @param array              $options
     * @param ExpressiveContract $model
     *
     * @return object|array|boolean
     *
     * @throws TException
     */
    public function select(
        array $arguments,
        array $options = [],
        ExpressiveContract $model
    ) {
        return $this->getSelectBuilder()->select(
            $arguments,
            $options,
            $model
        );
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return array|mixed
     *
     * @throws TException
     */
    public function search(ExpressiveContract $model)
    {
        return $this->getSelectBuilder()->search($model);
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
     * @param array              $arguments
     * @param ExpressiveContract $model
     *
     * @return int
     *
     * @throws TException
     */
    public function count(
        array $arguments = [],
        ExpressiveContract $model
    ) {
        return $this->getSelectBuilder()->count(
            $arguments,
            $model
        );
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     */
    public function last(ExpressiveContract $model)
    {
        return $this->getSelectBuilder()->last($model);
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

