<?php

namespace Solis\Expressive\Classes\Illuminate\Patch;

use Solis\Expressive\Classes\Illuminate\Insert\InsertBuilder;
use Solis\Expressive\Classes\Illuminate\Util\Actions;
use Solis\Expressive\Contracts\ExpressiveContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Breaker\TException;

/**
 * Class PatchBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Insert
 */
final class PatchBuilder
{

    /**
     * @var InsertBuilder
     */
    private $insertBuilder;

    /**
     * PatchBuilder constructor.
     */
    public function __construct()
    {
        $this->setInsertBuilder(new InsertBuilder());
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
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException;
     */
    public function patch(ExpressiveContract $model)
    {
        if (empty($model->getSchema()->getDatabase())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $original = $model->search();
        if (empty($original)) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'object for ' . get_class($model) . ' has not been found in the database',
                400
            );
        }

        Database::beginTransaction($model);

        try {

            if (empty($original->delete())) {
                throw new \PDOException('error removing original object');
            }

            $model = $this->setPrimaryKeysFromOriginal($original, $model);

            $record = $this->create($model);

        } catch (\PDOException $exception) {

            Database::rollbackActiveTransaction($model);
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                400
            );
        }

        Database::commitActiveTransaction($model);

        return $record;
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException;
     */
    private function create(ExpressiveContract $model)
    {
        $table = $model->getSchema()->getDatabase()->getTable();

        Database::beginTransaction($model);
        try {

            $model = Actions::doThingWhenDatabaseAction(
                $model,
                'whenInsert',
                'Before'
            );

            // verify direct dependencies to $model
            $model = $this->getInsertBuilder()->hasOneDependency($model);

            Capsule::table($table)->insert($this->getInsertBuilder()->getInsertFields($model));
        } catch (\PDOException $exception) {

            Database::rollbackActiveTransaction($model);
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                400
            );
        }

        // verify dependencies related to model
        $this->getInsertBuilder()->hasManyDependencies($model);

        Actions::doThingWhenDatabaseAction(
            $model,
            'whenInsert',
            'after'
        );

        Database::commitActiveTransaction($model);

        // return the last inserted entry
        return $model;
    }

    /**
     * @param ExpressiveContract $original
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     */
    private function setPrimaryKeysFromOriginal($original, $model)
    {
        foreach ($original->getSchema()->getDatabase()->getPrimaryKeys() as $primaryKey) {
            $model->$primaryKey = $original->$primaryKey;
        }

        $autoIncremented = array_filter($original->getSchema()->getProperties(), function ($property){
            return !empty($property->getBehavior()->isAutoIncrement()) ? true : false;
        });

        foreach ($autoIncremented as $field) {
            $model->{$field->getProperty()} = $original->{$field->getProperty()};
        }

        return $model;
    }
}
