<?php

namespace Solis\Expressive\Classes\Illuminate\Insert;

use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Breaker\TException;

/**
 * Class SelectBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Insert
 */
final class RelationshipBuilder
{

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     *
     * @return ExpressiveContract
     *
     * @throws TException
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
        if (empty($instance->search())) {
            $instance = $instance->create();
            if (empty($instance)) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "error creating dependency " . get_class($instance) . " for class " . get_class($model),
                    500
                );
            }
        }

        $refers = $dependency->getComposition()->getRelationship()->getSource()->getRefers();

        $field = $dependency->getComposition()->getRelationship()->getSource()->getField();

        $model->{$field} = $instance->{$refers};

        return $model;
    }

    /**
     * @param ExpressiveContract|ExpressiveContract[] $model
     * @param PropertyContract                        $dependency
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function hasMany(
        $model,
        $dependency
    ) {
        $dependencyValue = $model->{$dependency->getProperty()};

        $dependencyValue = !is_array($dependencyValue) ? [$dependencyValue] : $dependencyValue;

        $field = $dependency->getComposition()->getRelationship()->getSource()->getField();

        $refers = $dependency->getComposition()->getRelationship()->getSource()->getRefers();

        $sharedFields = $dependency->getComposition()->getRelationship()->getSharedFields();
        foreach ($dependencyValue as $item) {
            $item->$refers = $model->$field;

            if (!empty($sharedFields)) {
                foreach ($sharedFields as $sharedField) {
                    $item->{$sharedField} = $model->{$sharedField};
                }
            }
            if (!$item->create()) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "error creating dependency " . get_class($item) . " for " . get_class($model),
                    500
                );
            }
        }
    }
}
