<?php

namespace Solis\Expressive\Classes\Illuminate\Insert;

use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Expressive\Classes\Illuminate\Util\Actions;
use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Expressive\Exception;
use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;

/**
 * Class InsertBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Insert
 */
class InsertBuilder
{
    /**
     * @var RelationshipBuilder
     */
    protected $relationshipBuilder;

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
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TExceptionAbstract
     */
    public function create(ExpressiveContract $model)
    {
        $record = $this->beforeInsertVerifyDuplicity($model);
        if ($record) {
            return $record;
        }

        $table = $model::$schema->getRepository();

        Database::beginTransaction($model);
        try {
            $model = Actions::doThingWhenDatabaseAction($model, 'whenInsert', 'Before');

            $model = $this->hasOneDependency($model);

            Capsule::table($table)->insert($this->getInsertFields($model));
        } catch (\PDOException $exception) {
            Database::rollbackActiveTransaction($model);

            throw new Exception($exception->getMessage(), 400);
        }

        $model = $this->setPrimaryKeysFromLast($model);

        // verify dependencies related to model
        $this->hasManyDependencies($model);

        Actions::doThingWhenDatabaseAction($model, 'whenInsert', 'after');

        Database::commitActiveTransaction($model);

        return $model;
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     */
    public function setPrimaryKeysFromLast($model)
    {
        $last = $model->last(false);
        foreach ($model::$schema->getKeys() as $primaryKey) {
            $model->{$primaryKey->getProperty()} = $last->{$primaryKey->getProperty()};
        }

        // retorna a relação de campos incrementais a partir do schema
        // e atribui a instancia do model os valor atribuidos a instancia
        // do ultimo registro persistido para a respectivo classe
        $incrementalFields = $model::$schema->getIncrementalFieldsMeta();
        if ($incrementalFields) {
            foreach ($incrementalFields as $field) {
                $model->{$field->getProperty()} = $last->{$field->getProperty()};
            }
        }

        return $model;
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return bool|ExpressiveContract
     */
    private function beforeInsertVerifyDuplicity($model)
    {
        foreach ($model::$schema->getKeys() as $primaryKey) {
            if (is_null($model->{$primaryKey->getProperty()})) {
                return false;
            }
        }

        return $model->search(false);
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function hasOneDependency($model)
    {
        $dependencies = $model::$schema->getDependencies('hasOne');
        if (empty($dependencies)) {
            return $model;
        }

        foreach (array_values($dependencies) as $dependency) {
            $value = $model->{$dependency->getProperty()};

            if (!$value || is_array($value)) {
                continue;
            }

            $model = $this->getRelationshipBuilder()->hasOne($model, $dependency);
        }

        return $model;
    }

    /**
     * @param ExpressiveContract $model
     *
     * @throws TExceptionAbstract
     */
    public function hasManyDependencies($model)
    {
        $dependencies = $model::$schema->getDependencies('hasMany');
        if (!$dependencies) {
            return;
        }

        foreach (array_values($dependencies) as $dependency) {
            $value = $model->{$dependency->getProperty()};

            if (!$value) {
                continue;
            }

            $this->getRelationshipBuilder()->hasMany($model, $dependency);
        }
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return array
     *
     * @throws TExceptionAbstract
     */
    public function getInsertFields($model)
    {
        $persistentFields = $this->filterModelPersistentFields($model);

        // por alguma situação invalida no schema, pode ocorrer de o
        // registro não possuir campos a serem persistidos
        if (!$persistentFields) {
            throw new Exception(
                "class " . get_class($model) . " has not persistent fields",
                500
            );
        }

        $fields = $this->getPersistententValuesFromModel($model, $persistentFields);

        // caso houverem campos incrementais pela aplicação, consulta o ultimo registro de acordo
        // com os filtros do active record atribuido e atribui os valores  necessários.
        $incrementalFields = $model::$schema->getApplicationIncrementalFieldsMeta();
        if ($incrementalFields) {
            $fields = $this->setIncrementalFieldsFromLast($model, $incrementalFields, $fields);
        }

        return $fields;
    }

    /**
     * @param $model
     *
     * @return array
     */
    private function filterModelPersistentFields($model): array
    {
        $persistentFields = array_filter($model::$schema->getPersistentFields(), function ($item) use ($model) {
            $value = $model->{$item->getProperty()};

            $required = $item->getBehavior()->isRequired();
            // se o valor atruibo a propriede for null e seu comportamento
            // estiver classificado como não obrigatório, ela será excluida
            // da relação de campos para inserção
            if (is_null($value) && !$required) {
                return false;
            }

            // uma propriedade obrigatório não pose estar atribuida como valor
            // nulo no registro a ser persistido
            if (is_null($model->{$item->getProperty()}) && $required) {
                throw new Exception(
                    "a persistent field [ {$item->getProperty()} ] cannot be empty when inserting object "
                        . get_class($model),
                    400
                );
            }

            return true;
        });

        return $persistentFields;
    }

    /**
     * @param $model
     * @param $persistentFields
     *
     * @return array
     */
    private function getPersistententValuesFromModel($model, $persistentFields): array
    {
        $fields = [];
        foreach ($persistentFields as $persistentField) {
            // considerando que a entrada field corresponda ao campo na persistencia
            // captura o valor atribuido a entrada propriedade
            $fields[$persistentField->getField()] = $model->{$persistentField->getProperty()};
        }

        return $fields;
    }

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract[] $incrementalFields
     * @param array              $fields
     *
     * @return array
     */
    private function setIncrementalFieldsFromLast($model, $incrementalFields, $fields)
    {
        $last = $model->last(false);
        foreach ($incrementalFields as $incrementalField) {
            $value = $last->{$incrementalField->getProperty()} + 1;

            $fields[$incrementalField->getField()] = !empty($value) ? $value : 1;
        }

        return $fields;
    }
}
