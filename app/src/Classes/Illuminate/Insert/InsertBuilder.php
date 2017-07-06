<?php

namespace Solis\Expressive\Classes\Illuminate\Insert;

use Solis\Expressive\Classes\Illuminate\Util\Actions;
use Solis\Expressive\Abstractions\ExpressiveAbstract;
use Solis\PhpSchema\Abstractions\Database\FieldEntryAbstract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Breaker\TException;

/**
 * Class InsertBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Insert
 */
final class InsertBuilder
{
    /**
     * @var RelationshipBuilder
     */
    protected $relationshipBuilder;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->setRelationshipBuilder(new RelationshipBuilder());
    }

    /**
     * @return RelationshipBuilder
     */
    public function getRelationshipBuilder()
    {
        return $this->relationshipBuilder;
    }

    /**
     * @param RelationshipBuilder $relationshipBuilder
     */
    public function setRelationshipBuilder($relationshipBuilder)
    {
        $this->relationshipBuilder = $relationshipBuilder;
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
        if (empty($model->getSchema()->getDatabase())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $record = $this->beforeInsertVerifyDuplicity($model);
        if (!empty($record) && $record instanceof ExpressiveAbstract) {
            return $record;
        }

        $table = $model->getSchema()->getDatabase()->getTable();

        Database::beginTransaction($model);
        try {

            $model = Actions::doThingWhenDatabaseAction(
                $model,
                'whenInsert',
                'Before'
            );

            // verify direct dependencies to $model
            $model = $this->hasOneDependency($model);

            Capsule::table($table)->insert($this->getInsertFields($model));
        } catch (\PDOException $exception) {

            Database::rollbackActiveTransaction($model);
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                400
            );
        }

        $model = $this->setPrimaryKeysFromLast($model);

        // verify dependencies related to model
        $this->hasManyDependencies($model);

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
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     */
    private function setPrimaryKeysFromLast($model)
    {
        $last = $model->last();
        foreach ($model->getSchema()->getDatabase()->getPrimaryKeys() as $primaryKey) {
            $model->$primaryKey = $last->$primaryKey;
        }

        $autoIncremented = array_filter($model->getSchema()->getProperties(), function ($property){
            return !empty($property->getBehavior()->isAutoIncrement()) ? true : false;
        });

        foreach ($autoIncremented as $field) {
            $model->{$field->getProperty()} = $last->{$field->getProperty()};
        }

        return $model;
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return bool|ExpressiveContract
     */
    private function beforeInsertVerifyDuplicity($model)
    {
        foreach ($model->getSchema()->getDatabase()->getPrimaryKeys() as $primaryKey) {
            if (is_null($model->$primaryKey)) {
                return false;
            }
        }

        return $model->search();
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function hasOneDependency($model)
    {
        $dependencies = $model->getSchema()->getDatabase()->getByRelationshipType('hasOne');
        if (empty($dependencies)) {
            return $model;
        }

        foreach (array_values($dependencies) as $dependency) {
            $value = $model->{$dependency->getProperty()};

            if(!empty($value)){
                if (! $value instanceof ExpressiveAbstract) {
                    throw new TException(
                        __CLASS__,
                        __METHOD__,
                        "dependency must be instance of ExpressiveAbstract in class " . get_class($model),
                        500
                    );
                }

                $model = $this->getRelationshipBuilder()->hasOne(
                    $model,
                    $dependency
                );
            }
        }

        return $model;
    }

    /**
     * @param ExpressiveContract $model
     *
     * @throws TException
     */
    public function hasManyDependencies($model)
    {
        $dependencies = $model->getSchema()->getDatabase()->getByRelationshipType('hasMany');
        if (!empty($dependencies)) {
            foreach (array_values($dependencies) as $dependency) {
                $value = $model->{$dependency->getProperty()};
                if (!empty($value)) {
                    $this->getRelationshipBuilder()->hasMany(
                        $model,
                        $dependency
                    );
                }
            }
        }
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return array
     *
     * @throws TException
     */
    public function getInsertFields($model)
    {
        $persistentFields = array_filter($model->getSchema()->getDatabase()->getFields(), function (FieldEntryAbstract $item) use ($model){
            if (!empty($item->getBehavior()->isAutoIncrement()) && is_null($item->getProperty())) {
                return false;
            }
            if(!empty($item->getObject())){
                if($item->getObject()->getRelationship()->getType() === 'hasMany') {
                    return false;
                }
            }
            if (is_null($model->{$item->getProperty()}) && empty($item->getBehavior()->isRequired())) {
                return false;
            }
            if(is_null($model->{$item->getProperty()}) && $item->getBehavior()->isRequired()){
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "a persistent field [ {$item->getProperty()} ] cannot be empty when inserting object " . get_class($model),
                    400
                );
            }
            return true;
        });

        if (empty($persistentFields)) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                "class " . get_class($model) . " has not persistent fields",
                500
            );
        }

        $fields = [];
        foreach ($persistentFields as $persistentField) {
            $fields[$persistentField->getColumn()] = $model->{$persistentField->getProperty()};
        }

        return $fields;
    }
}
