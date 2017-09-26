<?php

namespace Solis\Expressive\Classes\Illuminate\Patch;

use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Expressive\Abstractions\ExpressiveAbstract;
use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Breaker\TException;

/**
 * Class RelationshipBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Patch
 */
final class RelationshipBuilder
{
    /**
     * @param ExpressiveContract $model
     * @param ExpressiveContract $original
     * @param PropertyContract   $dependency
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException
     */
    public function hasMany(
        $model,
        $original,
        $dependency
    ) {

        // remove todas as dependencias vinculados ao registro original
        $hasOriginalChanges = $this->parseOriginalDependencyValues(
            $original,
            $dependency
        );

        // verifica se serão criadas novas depencias para o respectivo model
        $hasNewChanges = $this->parseNewDependecyValues(
            $model,
            $dependency
        );

        // retorna true apenas se houveram alterações na persistência
        return !empty($hasOriginalChanges) || !empty($hasNewChanges) ? true : false;
    }

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     *
     * @return boolean
     *
     * @throws TExceptionAbstract
     */
    private function parseNewDependecyValues(
        $model,
        $dependency
    ) {
        $field = $dependency->getComposition()->getRelationship()->getSource()->getField();

        $refers = $dependency->getComposition()->getRelationship()->getSource()->getRefers();

        $dependencyArray = $model->{$dependency->getProperty()};

        /**
         * @var ExpressiveContract[] $dependencyArray
         */
        $dependencyArray = !is_array($dependencyArray) ? [$dependencyArray] : $dependencyArray;

        $dependencyArray = array_values(array_filter($dependencyArray, function ($item) {
            return $item instanceof ExpressiveAbstract ? true : false;
        }));

        if (empty($dependencyArray)) {
            // como não há dependências vinculadas, retorna false visto que não ocorreram alterações
            return false;
        }

        foreach ($dependencyArray as $dependencyValue) {
            /**
             * @var ExpressiveContract $dependencyValue
             */
            $dependencyValue->$refers = $model->$field;

            $sharedFields = $dependency->getComposition()->getRelationship()->getSharedFields();

            if (!empty($sharedFields)) {
                foreach ($sharedFields as $sharedField) {
                    $dependencyValue->$sharedField = $model->$sharedField;
                }
            }

            $dependencyValue->create();
        }

        // considera que ocorreram alterações devido a remoção das dependências
        return true;
    }

    /**
     * @param ExpressiveContract $original
     * @param PropertyContract   $dependency
     *
     * @return boolean
     *
     * @throws TExceptionAbstract
     */
    private function parseOriginalDependencyValues(
        $original,
        $dependency
    ) {
        $originalArray = $original->{$dependency->getProperty()};

        $originalArray = !is_array($originalArray) ? [$originalArray] : $originalArray;

        $originalArray = array_values(array_filter($originalArray, function ($item) {
            return $item instanceof ExpressiveAbstract ? true : false;
        }));

        if (empty($originalArray)) {
            // como não há dependências vinculadas, retorna false visto que não ocorreram alterações
            return false;
        }

        foreach ($originalArray as $originalDependency) {
            /**
             * @var ExpressiveContract $originalDependency
             */
            if (empty($originalDependency->delete())) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    'Error removing dependency has many in patch method',
                    500
                );
            }
        }

        // considera que ocorreram alterações devido a remoção das dependências
        return true;
    }
}
