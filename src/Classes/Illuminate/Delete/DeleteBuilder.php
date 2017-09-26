<?php

namespace Solis\Expressive\Classes\Illuminate\Delete;

use Solis\Expressive\Classes\Illuminate\Util\Actions;
use Solis\Expressive\Contracts\ExpressiveContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Breaker\TException;

/**
 * Class DeleteBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Delete
 */
class DeleteBuilder
{

    /**
     * @var RelationshipBuilder
     */
    protected $relationshipBuilder;

    /**
     * DeleteBuilder constructor.
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
     * @return boolean
     *
     * @throws TException;
     */
    public function delete(ExpressiveContract $model)
    {

        if (empty($model::$schema->getRepository())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
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
                'whenDelete',
                'before'
            );

            // remove has many dependencies of the model
            $this->hasManyDependencies($model);

            $result = $stmt->delete();
        } catch (\PDOException $exception) {
            Database::rollbackActiveTransaction($model);

            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                500
            );
        }

        $model = Actions::doThingWhenDatabaseAction(
            $model,
            'whenDelete',
            'after'
        );

        Database::commitActiveTransaction($model);

        return boolval($result);
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
}