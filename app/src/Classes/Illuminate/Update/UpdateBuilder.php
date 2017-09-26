<?php

namespace Solis\Expressive\Classes\Illuminate\Update;

use Solis\Expressive\Classes\Illuminate\Util\Actions;
use Solis\Expressive\Abstractions\ExpressiveAbstract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Breaker\TException;

/**
 * Class UpdateBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Insert
 */
final class UpdateBuilder
{

    /**
     * @var RelationshipBuilder
     */
    private $relationshipBuilder;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->setRelationshipBuilder(new RelationshipBuilder());
    }

    /**
     * @return RelationshipBuilder
     */
    public function getRelationshipBuilder()
    {
        return $this->relationshipBuilder;
    }

    /**
     * @param RelationshipBuilder $relationshipBuilder
     */
    public function setRelationshipBuilder($relationshipBuilder)
    {
        $this->relationshipBuilder = $relationshipBuilder;
    }

    /**
     * @param ExpressiveContract $model
     * @param boolean            $isPatch
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException;
     */
    public function update(ExpressiveContract $model, $isPatch = false)
    {
        if (empty($model::$schema->getRepository())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $original = $model->search();
        if (empty($original)) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'object for ' . get_class($model) . ' has not been found in the database',
                400
            );
        }

        $table = $model::$schema->getRepository();

        $primaryKeys = $model::$schema->getKeys();
        $stmt = Capsule::table($table);

        foreach ($primaryKeys as $key) {

            $value = $model->{$key->getProperty()};
            if (empty($value)) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "property '{$key}' used as primary key cannot be empty at " . get_class($model) . " instance",
                    400
                );
            }

            $stmt->where(
                $key->getField(),
                '=',
                $value
            );
        }

        Database::beginTransaction($model);

        try {

            $model = Actions::doThingWhenDatabaseAction(
                $model,
                'whenUpdate',
                'Before'
            );

            $model = $this->setPrimaryKeysFromOriginal($original, $model);

            $fields = $this->getUpdateFields(
                    $original,
                    $model,
                    $isPatch
            );

            if (empty($fields)) {
                Database::rollbackActiveTransaction($model);

                return false;
            }

            $stmt->update(
                $fields
            );
        } catch (\PDOException $exception) {

            Database::rollbackActiveTransaction($model);
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                400
            );
        }

        if (!$isPatch) {
            $this->hasManyDependencies($model);
        }

        $model = Actions::doThingWhenDatabaseAction(
            $model,
            'whenUpdate',
            'After'
        );

        Database::commitActiveTransaction($model);

        return true;
    }

    /**
     * @param ExpressiveContract $original
     * @param ExpressiveContract $updated
     * @param boolean            $isPatch
     *
     * @return array
     *
     * @throws TException
     */
    public function getUpdateFields(
            ExpressiveContract $original,
            ExpressiveContract $updated,
            $isPatch = false
    ) {

        // contém a relação de campos a serem atualizados
        $fields = [];

        foreach ($original::$schema->getPersistentFields() as $property) {
            // valor original de registro na persistencia
            $originalProperty = $original->{$property->getProperty()};
            // valor do registro atualizado
            $updatedProperty = $updated->{$property->getProperty()};

            // valida o comportamento do campo caso em operação de patch
            if ($isPatch && $property->getBehavior()->getWhenPatch()->getAction() === 'keep') {
                continue;
            }

            // registro em array é inválido para a atualização de valores
            // inicialmente, somente utilizada para relaciomaento hasMany.
            if (is_array($updatedProperty)) {
                continue;
            }

            // Update - na operação de update a alteração é desconsiderada caso a propriedade não possuir valor
            // e seu comportamento permitir valores em branco.
            // Patch - comportamento do patch preve substituição do campo se não fornecido para operação
            if (!$isPatch && is_null($updatedProperty) && !$property->getBehavior()->isRequired()) {
                continue;
            }

            // caso campo seja obrigatório e esteja como nulo no model a ser atualizado,
            // considera-se o valor do model original para operação
            if (is_null($updatedProperty) && $property->getBehavior()->isRequired()) {
                $updatedProperty = $originalProperty;
            }

            // se o valor atribuido ao registro a ser atualizado for também
            // instância de active record, deve-se captura o seu valor
            // chave comporando com o valor original
            if ($updatedProperty instanceof ExpressiveAbstract) {
                $result = $this->getUpdateFieldsExpressiveInstance(
                    $original,
                    $updated,
                    $property
                );

                if (!empty($result)) {
                    $fields = array_merge(
                        $fields,
                        $result
                    );
                }

                continue;
            }

            // somente considera como campo alteração caso valor no model
            // orginal for diferente do presente no model atualizado.
            if($originalProperty == $updatedProperty){
                continue;
            }

            $fields[$property->getProperty()] = $updatedProperty;
        }

        return $fields;
    }

    /**
     * @param ExpressiveContract $original
     * @param ExpressiveContract $updated
     * @param PropertyContract   $property
     *
     * @return array
     * @throws TException
     */
    public function getUpdateFieldsExpressiveInstance($original, $updated, $property)
    {
        $fields = [];

        $originalProperty = $original->{$property->getProperty()};

        $updatedProperty = $updated->{$property->getProperty()};

        $field = $property->getComposition()->getRelationship()->getSource()->getField();
        $refers = $property->getComposition()->getRelationship()->getSource()->getRefers();

        if ($originalProperty->{$refers} == $updatedProperty->{$refers}) {
            return [];
        }

        $fields[$field] = $updatedProperty->{$refers};

        return $fields;
    }

    /**
     * @param ExpressiveContract $model
     *
     * @throws TException
     */
    public function hasManyDependencies($model)
    {
        $dependencies = $model::$schema->getDependencies('hasMany');
        if (!empty($dependencies)) {
            foreach (array_values($dependencies) as $dependency) {
                $value = $model->{$dependency->getProperty()};
                if (!empty($value)) {
                    $this->getRelationshipBuilder()->hasMany(
                        $model,
                        $dependency
                    );
                }
            }
        }
    }

    /**
     * @param ExpressiveContract $original
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     */
    private function setPrimaryKeysFromOriginal($original, $model)
    {
        foreach ($original::$schema->getKeys() as $primaryKey) {
            $model->{$primaryKey->getProperty()} = $original->{$primaryKey->getProperty()};
        }

        // retorna a relação de campos incrementais a partir do schema
        // e atribui a instancia do model os valor atribuidos a instancia
        // do ultimo registro persistido para a respectivo classe
        $incrementalFields = $model::$schema->getIncrementalFieldsMeta();
        if (!empty($incrementalFields)) {
            foreach ($incrementalFields as $field) {
                $model->{$field->getProperty()} = $original->{$field->getProperty()};
            }
        }

        return $model;
    }
}
