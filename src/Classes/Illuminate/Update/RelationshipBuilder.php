<?php

namespace Solis\Expressive\Classes\Illuminate\Update;

use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Breaker\Abstractions\TExceptionAbstract;

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
     * @throws TExceptionAbstract
     */
    public function hasMany(
        $model,
        $dependency
    ) {
        $field = $dependency->getComposition()->getRelationship()->getSource()->getField();

        $refers = $dependency->getComposition()->getRelationship()->getSource()->getRefers();

        $dependencyArray = $model->{$dependency->getProperty()};
        $dependencyArray = !is_array($dependencyArray) ? [$dependencyArray] : $dependencyArray;

        foreach ($dependencyArray as $dependencyValue) {
            $dependencyValue->$refers = $model->$field;

            $sharedFields = $dependency->getComposition()->getRelationship()->getSharedFields();
            if ($sharedFields) {
                foreach ($sharedFields as $sharedField) {
                    $dependencyValue->$sharedField = $model->$sharedField;
                }
            }

            $commonFields = $dependency->getComposition()->getRelationship()->getCommonFields();
            if ($commonFields) {
                foreach ($commonFields as $commonField) {
                    $dependencyValue->$commonField = $model->$commonField;
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
