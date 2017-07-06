<?php

namespace Solis\Expressive\Classes\Illuminate\Update;

use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\PhpSchema\Abstractions\Database\FieldEntryAbstract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Wrapper;
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
     * @param FieldEntryAbstract $dependency
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function hasMany($model, $dependency)
    {
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
            $dependencyInstance = $dependencyValue->search();
            if (empty($dependencyInstance)) {
                $dependencyValue->create();
            } else {
                $dependencyValue->update();
            }
        }
    }
}
