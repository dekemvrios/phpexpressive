<?php

namespace Solis\Expressive\Classes\Illuminate\Delete;

use Solis\Expressive\Classes\Illuminate\Util\Actions;
use Solis\Expressive\Contracts\ExpressiveContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Expressive\Exception;
use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;

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
     * @throws TExceptionAbstract
     */
    public function delete(ExpressiveContract $model)
    {
        $table = $model::$schema->getRepository();

        Database::beginTransaction($model);

        $stmt = $this->buildDeleteStmt($model, $table);

        try {

            $model = Actions::doThingWhenDatabaseAction($model, 'whenDelete', 'before');

            $this->deleteHasMany($model);

            $result = $stmt->delete();
        } catch (\PDOException $exception) {
            Database::rollbackActiveTransaction($model);

            throw new Exception($exception->getMessage(), 500);
        }

        $model = Actions::doThingWhenDatabaseAction($model, 'whenDelete', 'after');

        Database::commitActiveTransaction($model);

        return boolval($result);
    }

    /**
     * @param ExpressiveContract $model
     * @param                    $table
     *
     * @return \Illuminate\Database\Query\Builder
     * @throws Exception
     */
    private function buildDeleteStmt(ExpressiveContract $model, $table)
    {
        $primaryKeys = $model::$schema->getKeys();

        $stmt = Capsule::table($table);

        foreach ($primaryKeys as $key) {
            $stmt = $this->setPropertyInWhereStmt($model, $key, $stmt);
        }

        return $stmt;
    }

    /**
     * @param ExpressiveContract                 $model
     * @param PropertyContract                   $property
     * @param \Illuminate\Database\Query\Builder $stmt
     *
     * @throws Exception
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private function setPropertyInWhereStmt(ExpressiveContract $model, $property, $stmt)
    {
        $value = $model->{$property->getProperty()};

        if (!$value) {
            throw new Exception(
                    "property '{$property->getProperty()}' used as primary can't be null.",
                    400
            );
        }

        $stmt->where($property->getField(), '=', $value);

        return $stmt;
    }

    /**
     * @param ExpressiveContract $model
     *
     * @throws TExceptionAbstract
     */
    private function deleteHasMany($model)
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
}
