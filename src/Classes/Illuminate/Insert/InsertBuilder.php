<?php

namespace Solis\Expressive\Classes\Illuminate\Insert;

use Solis\Expressive\Classes\Illuminate\Util\Actions;
use Solis\Expressive\Abstractions\ExpressiveAbstract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Breaker\TException;

/**
 * Class InsertBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Insert
 */
final class InsertBuilder
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
        if (empty($model::$schema->getRepository())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $record = $this->beforeInsertVerifyDuplicity($model);
        if (!empty($record) && $record instanceof ExpressiveAbstract) {
            return $record;
        }

        $table = $model::$schema->getRepository();

        Database::beginTransaction($model);
        try {
            $model = Actions::doThingWhenDatabaseAction(
                $model,
                'whenInsert',
                'Before'
            );

            // verify direct dependencies to $model
            $model = $this->hasOneDependency($model);

            Capsule::table($table)->insert($this->getInsertFields($model));
        } catch (\PDOException $exception) {
            Database::rollbackActiveTransaction($model);
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                400
            );
        }

        $model = $this->setPrimaryKeysFromLast($model);

        // verify dependencies related to model
        $this->hasManyDependencies($model);

        Actions::doThingWhenDatabaseAction(
            $model,
            'whenInsert',
            'after'
        );

        Database::commitActiveTransaction($model);

        // return the last inserted entry
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
        if (!empty($incrementalFields)) {
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

            if (!empty($value)) {
                if (!$value instanceof ExpressiveAbstract) {
                    throw new TException(
                        __CLASS__,
                        __METHOD__,
                        "dependency must be instance of ExpressiveAbstract in class " . get_class($model),
                        500
                    );
                }

                $model = $this->getRelationshipBuilder()->hasOne(
                    $model,
                    $dependency
                );
            }
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
     * @param ExpressiveContract $model
     *
     * @return array
     *
     * @throws TExceptionAbstract
     */
    public function getInsertFields($model)
    {
        $persistentFields = array_filter($model::$schema->getPersistentFields(), function ($item) use ($model) {
            $value = $model->{$item->getProperty()};

            $required = $item->getBehavior()->isRequired();
            // se o valor atruibo a propriede for null e seu comportamento
            // estiver classificado como não obrigatório, ela será excluida
            // da relação de campos para inserção
            if (is_null($value) && empty($required)) {
                return false;
            }

            // uma propriedade obrigatório não pose estar atribuida como valor
            // nulo no registro a ser persistido
            if (is_null($model->{$item->getProperty()}) && !empty($required)) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "a persistent field [ {$item->getProperty()} ] cannot be empty when inserting object "
                    . get_class($model),
                    400
                );
            }
            return true;
        });

        // por alguma situação invalida no schema, pode ocorrer de o
        // registro não possuir campos a serem persistidos
        if (empty($persistentFields)) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                "class " . get_class($model) . " has not persistent fields",
                500
            );
        }

        $fields = [];
        foreach ($persistentFields as $persistentField) {
            // considerando que a entrada field corresponda ao campo na persistencia
            // captura o valor atribuido a entrada propriedade
            $fields[$persistentField->getField()] = $model->{$persistentField->getProperty()};
        }

        // caso houverem campos incrementais pela aplicação, consulta o ultimo registro de acordo
        // com os filtros do active record atribuido e atribui os valores  necessários.
        $applicationIncrementalFieldsMeta = $model::$schema->getApplicationIncrementalFieldsMeta();
        if (!empty($applicationIncrementalFieldsMeta)) {
            $last = $model->last(false);
            foreach ($applicationIncrementalFieldsMeta as $incrementalField) {
                $value = $last->{$incrementalField->getProperty()} + 1;

                $fields[$incrementalField->getField()] = !empty($value) ? $value : 1;
            }
        }

        return $fields;
    }
}
