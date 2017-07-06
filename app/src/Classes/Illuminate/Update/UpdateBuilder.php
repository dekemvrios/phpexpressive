<?php

namespace Solis\Expressive\Classes\Illuminate\Update;

use Solis\Expressive\Classes\Illuminate\Util\Actions;
use Solis\Expressive\Abstractions\ExpressiveAbstract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Database;
use Solis\PhpSchema\Abstractions\Properties\PropertyEntryAbstract;
use Solis\Breaker\TException;

/**
 * Class UpdateBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Insert
 */
final class UpdateBuilder
{

    /**
     * @var RelationshipBuilder
     */
    private $relationshipBuilder;

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
    public function update(ExpressiveContract $model)
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

        $table = $model->getSchema()->getDatabase()->getTable();

        $primaryKeys = $model->getSchema()->getDatabase()->getPrimaryKeys();
        $stmt = Capsule::table($table);

        foreach ($primaryKeys as $key) {

            $value = $model->{$key};
            if (empty($value)) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "property '{$key}' used as primary key cannot be empty at " . get_class($model) . " instance",
                    400
                );
            }

            $stmt->where(
                $key,
                '=',
                $value
            );
        }

        Database::beginTransaction($model);

        try {

            $model = Actions::doThingWhenDatabaseAction(
                $model,
                'whenUpdate',
                'Before'
            );

            $model = $this->setPrimaryKeysFromOriginal($original, $model);

            $fields = $this->getUpdateFields(
                $original,
                $model
            );

            if (empty($fields)) {
                return false;
            }

            $stmt->update(
                $fields
            );
        } catch (\PDOException $exception) {

            Database::rollbackActiveTransaction($model);
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                400
            );
        }

        $this->hasManyDependencies($model);

        $model = Actions::doThingWhenDatabaseAction(
            $model,
            'whenUpdate',
            'After'
        );

        Database::commitActiveTransaction($model);

        return true;
    }

    /**
     * @param ExpressiveContract $original
     * @param ExpressiveContract $updated
     *
     * @return array
     *
     * @throws TException
     */
    public function getUpdateFields(
        ExpressiveContract $original,
        ExpressiveContract $updated
    ) {

        $properties = $original->getSchema()->getProperties();

        $fields = [];
        foreach ($properties as $property) {
            $originalProperty = $original->{$property->getProperty()};
            $updatedProperty = $updated->{$property->getProperty()};

            if (!empty($property->getObject()) && $property->getObject()->getRelationship()->getType() === 'hasMany') {
                continue;
            }

            if ($originalProperty instanceof ExpressiveAbstract) {
                $result = $this->getUpdateFieldsExpressiveInstance(
                    $original,
                    $updated,
                    $property
                );
                if (!empty($result)) {
                    $fields = array_merge(
                        $fields,
                        $result
                    );
                }
            } elseif (!is_array($updatedProperty)) {

                if (is_null($updatedProperty) && empty($property->getBehavior()->isRequired())) {
                    continue;
                }

                if (is_null($updatedProperty) && !empty($property->getBehavior()->isRequired())) {
                    $fields[$property->getProperty()] = $originalProperty;

                    continue;
                }

                if ($originalProperty !== $updatedProperty) {
                    $fields[$property->getProperty()] = $updatedProperty;
                }
            }
        }

        return $fields;
    }

    /**
     * @param ExpressiveContract    $original
     * @param ExpressiveContract    $updated
     * @param PropertyEntryAbstract $property
     *
     * @return array
     * @throws TException
     */
    public function getUpdateFieldsExpressiveInstance($original, $updated, $property)
    {
        $fields = [];

        $databaseEntry = $original->getSchema()->getDatabase()->getEntry(
            'property',
            $property->getProperty()
        );
        if(empty($databaseEntry)){
            throw new TException(
                __CLASS__,
                __METHOD__,
                "not found database entry for property " . $property->getProperty() . " while updating record",
                500
            );
        }

        $originalProperty = $original->{$property->getProperty()};
        $updatedProperty = $updated->{$property->getProperty()};

        $databaseEntry = array_values($databaseEntry);

        if ($databaseEntry[0]->getObject()->getRelationship()->getType() === 'hasOne') {
            $field = $databaseEntry[0]->getObject()->getRelationship()->getSource()->getField();
            $refers = $databaseEntry[0]->getObject()->getRelationship()->getSource()->getRefers();

            if ($originalProperty->{$refers} !== $updatedProperty->{$refers}) {
                $fields[$field] = $updatedProperty->{$refers};
            }
        }

        return $fields;
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
