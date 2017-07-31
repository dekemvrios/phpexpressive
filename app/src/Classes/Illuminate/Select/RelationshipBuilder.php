<?php

namespace Solis\Expressive\Classes\Illuminate\Select;

use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\PhpSchema\Abstractions\Database\FieldEntryAbstract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Wrapper;
use Solis\Breaker\TException;

/**
 * Class SelectBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Select
 */
final class RelationshipBuilder
{

    /**
     * @param ExpressiveContract    $model
     * @param FieldEntryAbstract $dependency
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function hasOne($model, $dependency)
    {
        $dependencyCode = $value = $model->{$dependency->getProperty()};

        if(empty($dependencyCode)){
            return $model;
        }

        // get dependency class
        $dependencyClass = $dependency->getObject()->getClass();

        // instantiate it
        $instance = new $dependencyClass();

        // static search for test
        $refers = $dependency->getObject()->getRelationship()->getSource()->getRefers();

        // defines the main pr property value
        $instance->{$refers} = $dependencyCode;

        if (!empty($dependency->getObject()->getRelationship()->getSharedFields())) {
            foreach ($dependency->getObject()->getRelationship()->getSharedFields() as $field) {
                $instance->{$field} = $model->{$field};
            }
        }

        $instance = $instance->search();
        if (empty($instance)) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                "dependency {$dependencyClass} not found for class " . get_class($model),
                400
            );
        }

        $model->{$dependency->getProperty()} = $instance;

        return $model;
    }

    /**
     * @param ExpressiveContract    $model
     * @param FieldEntryAbstract $dependency
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function hasMany($model, $dependency)
    {
        // get dependency class
        $dependencyClass = $dependency->getObject()->getClass();

        $instance = new $dependencyClass();

        $field = $dependency->getObject()->getRelationship()->getSource()->getField();

        // static search for test
        $refers = $dependency->getObject()->getRelationship()->getSource()->getRefers();

        // get dependency schema table name
        $table = $instance->getSchema()->getDatabase()->getTable();

        $stmt = Capsule::table($table);
        $stmt->where(
            $refers,
            '=',
            $model->{$field}
        );

        $sharedFields = $dependency->getObject()->getRelationship()->getSharedFields();
        if (!empty($sharedFields)) {
            foreach ($sharedFields as $sharedField) {
                $stmt->where(
                    $sharedField,
                    '=',
                    $model->{$sharedField}
                );
            }
        }

        try {
            $result = $stmt->get()->toArray();
        } catch (\PDOException $exception) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                400
            );
        }

        if (empty($result)) {
            return $model;
        }

        $hasMany = [];
        foreach ($result as $item) {
            $hasManyItem = Wrapper::fetchStdClassToExpressiveModel(
                $item,
                new $dependencyClass()
            );
            if (!empty($hasManyItem)) {
                $hasManyItem = (new SelectBuilder())->searchForDependencies($hasManyItem, true);

                $hasMany[] = $hasManyItem;
            }
        }
        if (!empty($hasMany)) {
            $model->{$dependency->getProperty()} = count($hasMany) > 1 ? $hasMany : $hasMany[0];
        }
        return $model;
    }
}
