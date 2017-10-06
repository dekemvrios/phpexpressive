<?php

namespace Solis\Expressive\Classes\Illuminate\Insert;

use Solis\Expressive\Abstractions\ExpressiveAbstract;
use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Expressive\Exception;

/**
 * Class SelectBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Insert
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
    public function hasOne(
        $model,
        $dependency
    ) {
        $instance = $this->getDependencyInstance($model, $dependency);

        if ($this->hasSharedFields($dependency)) {
            $instance = $this->shareFieldsBetweenInstances($model, $dependency, $instance);
        }

        if (!$instance->search(false)) {
            $instance = $instance->create();

            if (!$instance) {
                throw new Exception(
                        "error creating dependency " . get_class($instance) . " for class " . get_class($model),
                        500
                );
            }
        }

        return $this->setDependencyKeyToModel($model, $dependency, $instance);
    }

    /**
     * @param ExpressiveContract|ExpressiveContract[] $model
     * @param PropertyContract                        $dependency
     *
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function hasMany(
        $model,
        $dependency
    ) {
        $dependencyValue = $this->getDependencyValue($model, $dependency);

        $field = $this->getCompositionField($dependency);

        $refers = $this->getCompositionRefers($dependency);

        foreach ($dependencyValue as $item) {
            $item->$refers = $model->$field;

            if ($this->hasSharedFields($dependency)) {
                $item = $this->shareFieldsBetweenInstances($model, $dependency, $item);
            }

            if (!$item->create()) {
                throw new Exception(
                    "error creating dependency " . get_class($item) . " for " . get_class($model),
                    500
                );
            }
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
     * @return array|string
     */
    private function getCompositionSharedFields(PropertyContract $dependency)
    {
        $sharedFields = $dependency->getComposition()->getRelationship()->getSharedFields();

        return $sharedFields;
    }

    /**
     * @param PropertyContract $dependency
     *
     * @return string
     */
    private function getCompositionField(PropertyContract $dependency)
    {
        return $dependency->getComposition()->getRelationship()->getSource()->getField();
    }

    /**
     * @param PropertyContract $dependency
     *
     * @return string
     */
    private function getCompositionRefers(PropertyContract $dependency)
    {
        return $dependency->getComposition()->getRelationship()->getSource()->getRefers();
    }

    /**
     * @param $model
     * @param $dependency
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
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     * @param ExpressiveContract $instance
     *
     * @return ExpressiveContract
     */
    private function setDependencyKeyToModel($model, $dependency, $instance)
    {
        $refers = $this->getCompositionRefers($dependency);

        $field = $this->getCompositionField($dependency);

        $model->{$field} = $instance->{$refers};

        return $model;
    }

    /**
     * @param $model
     * @param $dependency
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function getDependencyInstance($model, $dependency): mixed
    {
        $value = $model->{$dependency->getProperty()};
        $class = $dependency->getComposition()->getClass();

        if (is_array($value)) {
            return call_user_func_array([$class, 'make'], [$value]);
        }

        if (!($value instanceof ExpressiveAbstract)) {
            throw new Exception('Invalid dependency value for insert builder', 400);
        }

        return $value;
    }
}
