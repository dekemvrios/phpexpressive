<?php

namespace Solis\Expressive\Classes\Illuminate\Insert;

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
        $value = $model->{$dependency->getProperty()};
        $instance = is_array($dependency) ? call_user_func_array(
            [$dependency->getComposition()->getClass(), 'make'],
            [$model->{$dependency->getProperty()}]
        ) : $value;

        $sharedFields = $dependency->getComposition()->getRelationship()->getSharedFields();
        if (!empty($sharedFields)) {
            foreach ($sharedFields as $sharedField) {
                $instance->{$sharedField} = $model->{$sharedField};
            }
        }
        if (empty($instance->search(false))) {
            $instance = $instance->create();
            if (empty($instance)) {
                throw new Exception(
                    "error creating dependency " . get_class($instance) . " for class " . get_class($model),
                    500
                );
            }
        }

        $refers = $this->getCompositionRefers($dependency);

        $field = $this->getCompositionField($dependency);

        $model->{$field} = $instance->{$refers};

        return $model;
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
        $dependencyValue = $model->{$dependency->getProperty()};

        $dependencyValue = !is_array($dependencyValue) ? [$dependencyValue] : $dependencyValue;

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
}
