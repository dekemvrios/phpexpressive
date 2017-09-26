<?php

namespace Solis\Expressive\Classes\Illuminate\Replicate;

use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Classes\Illuminate\Insert\InsertBuilder;
use Solis\Expressive\Abstractions\ExpressiveAbstract;
use Solis\Expressive\Classes\Illuminate\Util\Actions;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Expressive\Schema\Contracts\SchemaContract;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Breaker\TException;

/**
 * Class PatchBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Insert
 */
final class ReplicateBuilder
{
    /**
     * @var InsertBuilder
     */
    private $insertBuilder;
    /**
     * PatchBuilder constructor.
     */
    public function __construct()
    {
        $this->setInsertBuilder(new InsertBuilder());
    }
    /**
     * @return InsertBuilder
     */
    public function getInsertBuilder()
    {
        return $this->insertBuilder;
    }
    /**
     * @param InsertBuilder $insertBuilder
     */
    public function setInsertBuilder($insertBuilder)
    {
        $this->insertBuilder = $insertBuilder;
    }
    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TExceptionAbstract
     */
    public function replicate(ExpressiveContract $model)
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
        return $this->create($original);
    }
    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TExceptionAbstract
     */
    private function create(
        ExpressiveContract $model
    ) {

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

            $insertFields = $this->getInsertBuilder()->getInsertFields($model);

            $replicateFields = $this->handleFieldsOnReplicateAction($model, $insertFields);

            Capsule::table($table)->insert($replicateFields);
        } catch (\PDOException $exception) {
            Database::rollbackActiveTransaction($model);
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                400
            );
        }
        $model = $this->getInsertBuilder()->setPrimaryKeysFromLast($model);

        // verify dependencies related to model
        $this->hasManyDependencies($model);

        Actions::doThingWhenDatabaseAction(
            $model,
            'whenInsert',
            'after'
        );

        Database::commitActiveTransaction($model);
        // return the last inserted entry
        return $model->search();
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
                if (! $value instanceof ExpressiveAbstract) {
                    throw new TException(
                        __CLASS__,
                        __METHOD__,
                        "dependency must be instance of ExpressiveAbstract in class " . get_class($model),
                        500
                    );
                }
                $model = $this->hasOne(
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
        if (empty($dependencies)) {
            return;
        }

        foreach (array_values($dependencies) as $dependency) {
            $value = $model->{$dependency->getProperty()};
            if (!empty($value)) {
                $this->hasMany(
                    $model,
                    $dependency
                );
            }
        }
    }

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     *
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function hasOne(
        $model,
        $dependency
    ) {
        $value = $model->{$dependency->getProperty()};

        $instance = is_array($dependency) ? call_user_func_array(
            [$dependency->getComposition()->getClass(), 'make'],
            [$model->{$dependency->getProperty()}]
        ) : $value;

        $sharedFields = $dependency->getComposition()->getRelationship()->getSharedFields();
        if (!empty($sharedFields)) {
            foreach ($sharedFields as $sharedField) {
                $instance->{$sharedField} = $model->{$sharedField};
            }
        }

        if (empty($instance->search())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                "dependency " . get_class($instance) . " has not been found in database for class " . get_class($model),
                500
            );
        }

        $refers = $dependency->getComposition()->getRelationship()->getSource()->getRefers();
        $field = $dependency->getComposition()->getRelationship()->getSource()->getField();

        $model->{$field} = $instance->{$refers};

        return $model;
    }

    /**
     * @param ExpressiveContract|ExpressiveContract[] $model
     * @param PropertyContract                        $dependency
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

        if (empty($dependencyValue)) {
            return;
        }

        if ($dependency->getBehavior()->getWhenReplicate()->getAction() === 'clean') {
            return;
        }

        $field = $dependency->getComposition()->getRelationship()->getSource()->getField();

        $refers = $dependency->getComposition()->getRelationship()->getSource()->getRefers();

        $sharedFields = $dependency->getComposition()->getRelationship()->getSharedFields();
        foreach ($dependencyValue as $item) {
            $item->$refers = $model->$field;

            if (!empty($sharedFields)) {
                foreach ($sharedFields as $sharedField) {
                    $item->{$sharedField} = $model->{$sharedField};
                }
            }

            if (!$this->create($item)) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "error creating dependency " . get_class($item) . " for " . get_class($model),
                    500
                );
            }
        }
    }


    /**
     * @param ExpressiveContract $model
     * @param array              $insertFields
     *
     * @return array
     */
    private function handleFieldsOnReplicateAction($model, $insertFields)
    {

        /**
         * @var SchemaContract $schema
         */
        $schema = $model::$schema;

        foreach ($schema->getProperties() as $property) {
            if (empty($property->getBehavior()->getWhenReplicate())) {
                continue;
            }

            if (!in_array($property->getField(), array_keys($insertFields))) {
                continue;
            }

            $value = $insertFields[$property->getField()];
            switch ($property->getBehavior()->getWhenReplicate()->getAction()) {
                case 'keep':
                    break;
                case 'static':
                    $value = $property->getBehavior()->getWhenReplicate()->getValue();
                    break;
                case 'clean':
                    $value = null;
                    break;
                case 'last+1':
                    $last = $model->last();
                    if (empty($last)) {
                        break;
                    }

                    $lastValue = $last->{$property->getProperty()};

                    $value = !empty($lastValue) ? $lastValue + 1 : 1;
                    break;
            }

            $insertFields[$property->getField()] = $value;
        }

        return $insertFields;
    }
}
