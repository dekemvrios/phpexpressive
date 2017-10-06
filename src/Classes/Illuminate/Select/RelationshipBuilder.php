<?php

namespace Solis\Expressive\Classes\Illuminate\Select;

use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Classes\Illuminate\Diglett;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Expressive\Exception;

/**
 * Class RelationshipBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Select
 */
class RelationshipBuilder
{

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     *
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function hasOne($model, $dependency)
    {
        $key = $model->{$dependency->getProperty()};

        if (!$key) {
            return $model;
        }

        $instance = $this->searchHasOne($model, $dependency);

        if (!$instance) {
            throw new Exception(
                "dependency {$dependency->getProperty()} not found for class " . get_class($model),
                400
            );
        }

        $model->{$dependency->getProperty()} = $instance;

        return $model;
    }

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     *
     * @return bool|ExpressiveContract
     */
    private function searchHasOne($model, PropertyContract $dependency)
    {
        $instance = $this->getDependencyInstance($dependency);

        $refers = $this->getCompositionRefers($dependency);

        $instance->{$refers} = $model->{$dependency->getProperty()};

        if ($this->hasSharedFields($dependency)) {
            $instance = $this->shareFieldsBetweenInstances($model, $dependency, $instance);
        }

        $instance = $instance->search();

        return $instance;
    }

    /**
     * @param PropertyContract $dependency
     *
     * @return ExpressiveContract
     */
    private function getDependencyInstance(PropertyContract $dependency)
    {
        $class = $this->getDependencyClass($dependency);

        return new $class();
    }

    /**
     * @param PropertyContract $dependency
     *
     * @return string
     */
    private function getDependencyClass(PropertyContract $dependency)
    {
        return $dependency->getComposition()->getClass();
    }

    /**
     * @param PropertyContract $dependency
     *
     * @return mixed
     */
    private function getCompositionRefers(PropertyContract $dependency)
    {
        $refers = $dependency->getComposition()->getRelationship()->getSource()->getRefers();

        return $refers;
    }

    /**
     * @param PropertyContract $dependency
     *
     * @return bool
     */
    private function hasSharedFields(PropertyContract $dependency): bool
    {
        return !empty($dependency->getComposition()->getRelationship()->getSharedFields());
    }

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     * @param ExpressiveContract $instance
     *
     * @return ExpressiveContract
     */
    private function shareFieldsBetweenInstances($model, $dependency, $instance)
    {
        foreach ($this->getCompositionSharedFields($dependency) as $field) {
            $instance->{$field} = $model->{$field};
        }

        return $instance;
    }

    /**
     * @param PropertyContract $dependency
     *
     * @return mixed
     */
    private function getCompositionSharedFields(PropertyContract $dependency)
    {
        $sharedFields = $dependency->getComposition()->getRelationship()->getSharedFields();

        return $sharedFields;
    }

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     *
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function hasMany($model, $dependency)
    {
        $stmt = $this->buildHasManyStmt($model, $dependency);

        try {
            $rows = $stmt->get()->toArray();
        } catch (\PDOException $exception) {
            throw new Exception($exception->getMessage(), 400);
        }

        if (!$rows) {
            return $model;
        }

        $hasMany = $this->fetchRowsAsModelArray($dependency, $rows);

        $model->{$dependency->getProperty()} = $hasMany;

        return $model;
    }

    /**
     * @param $model
     * @param $dependency
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private function buildHasManyStmt($model, $dependency): \Illuminate\Database\Query\Builder
    {
        $instance = $this->getDependencyInstance($dependency);

        $field = $this->getCompositionField($dependency);

        // static search for test
        $refers = $this->getCompositionRefers($dependency);

        // get dependency schema table name
        $table = $instance::$schema->getRepository();

        $stmt = Capsule::table($table);

        $stmt->where($refers, '=', $model->{$field});

        if ($this->hasSharedFields($dependency)) {
            $stmt = $this->setWhereForSharedFields($model, $dependency, $stmt);
        }

        return $stmt;
    }

    /**
     * @param PropertyContract $dependency
     *
     * @return mixed
     */
    private function getCompositionField(PropertyContract $dependency)
    {
        $field = $dependency->getComposition()->getRelationship()->getSource()->getField();

        return $field;
    }

    /**
     * @param ExpressiveContract                 $model
     * @param PropertyContract                   $dependency
     * @param \Illuminate\Database\Query\Builder $stmt
     *
     * @return \Illuminate\Database\Query\Builder;
     */
    private function setWhereForSharedFields($model, $dependency, $stmt)
    {
        $sharedFields = $this->getCompositionSharedFields($dependency);

        foreach ($sharedFields as $sharedField) {
            $stmt->where($sharedField, '=', $model->{$sharedField});
        }

        return $stmt;
    }

    /**
     * @param $dependency
     * @param $rows
     *
     * @return array
     */
    private function fetchRowsAsModelArray($dependency, $rows): array
    {
        $selectBuilder = new SelectBuilder();

        $hasMany = [];
        foreach ($rows as $item) {
            $model = $selectBuilder->makeNewExpressiveModel($item, $this->getDependencyClass($dependency));

            if (!$model) {
                continue;
            }

            if (Diglett::toDig()) {
                $model = $selectBuilder->getModelRelationships($model, true);
            }

            $hasMany[] = $model;
        }

        return $hasMany;
    }
}
