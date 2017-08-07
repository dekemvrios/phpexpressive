<?php

namespace Solis\Expressive\Classes\Illuminate\Update;

use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Breaker\TException;

/**
 * Class RelationshipBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Update
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
    public function hasMany(
        $model,
        $dependency
    ) {
        $field = $dependency->getObject()->getRelationship()->getSource()->getField();

        $refers = $dependency->getObject()->getRelationship()->getSource()->getRefers();

        $dependencyArray = $model->{$dependency->getProperty()};
        $dependencyArray = !is_array($dependencyArray) ? [$dependencyArray] : $dependencyArray;

        foreach ($dependencyArray as $dependencyValue) {

            $dependencyValue->$refers = $model->$field;
            $sharedFields = $dependency->getObject()->getRelationship()->getSharedFields();
            if (!empty($sharedFields)) {
                foreach ($sharedFields as $sharedField) {
                    $dependencyValue->$sharedField = $model->$sharedField;
                }
            }
            $dependencyInstance = $dependencyValue->search(false);
            if (empty($dependencyInstance)) {
                $dependencyValue->create();
            } else {
                $dependencyValue->update();
            }
        }
    }
}
