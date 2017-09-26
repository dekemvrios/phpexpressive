<?php

namespace Solis\Expressive\Classes\Illuminate\Delete;

use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Breaker\TException;

/**
 * Class SelectBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Delete
 */
final class RelationshipBuilder
{

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
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

        $sharedFields = $dependency->getComposition()->getRelationship()->getSharedFields();
        foreach ($dependencyValue as $item) {
            if (!empty($sharedFields)) {
                foreach ($sharedFields as $sharedField) {
                    $item->{$sharedField} = $model->{$sharedField};
                }
            }

            $child = $item->search();
            if (empty($child)) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "error deleting dependency " . get_class($item) . " for " . get_class($model),
                    500
                );
            }
            $child->delete();
        }
    }
}
