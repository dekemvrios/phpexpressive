<?php

namespace Solis\Expressive\Classes\Illuminate\Delete;

use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Expressive\Exception;

/**
 * Class RelationshipBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Delete
 */
class RelationshipBuilder
{

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     *
     * @throws TExceptionAbstract
     */
    public function hasMany(
        $model,
        $dependency
    ) {
        $dependencyValue = $this->getDependencyValue($model, $dependency);
        if (!$dependencyValue) {
            return;
        }

        $this->deleteHasMany($model, $dependency, $dependencyValue);
    }

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     *
     * @return array
     */
    private function getDependencyValue($model, PropertyContract $dependency): array
    {
        $dependencyValue = $model->{$dependency->getProperty()};

        $dependencyValue = !is_array($dependencyValue) ? [$dependencyValue] : $dependencyValue;

        return $dependencyValue;
    }

    /**
     * @param $model
     * @param $dependency
     * @param $dependencyValue
     *
     * @throws Exception
     */
    private function deleteHasMany($model, $dependency, $dependencyValue): void
    {
        foreach ($dependencyValue as $item) {

            if ($this->hasSharedFields($dependency)) {
                $item = $this->shareFieldsBetweenInstances($model, $dependency, $item);
            }

            $child = $item->search();

            if (!$child) {
                throw new Exception(
                        "error deleting dependency " . get_class($item) . " for " . get_class($model), 500
                );
            }

            $child->delete();
        }
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
}
