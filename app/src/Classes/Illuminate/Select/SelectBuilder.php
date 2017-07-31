<?php

namespace Solis\Expressive\Classes\Illuminate\Select;

use Solis\Expressive\Contracts\ExpressiveContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Wrapper;
use Solis\Breaker\TException;
use Solis\PhpSchema\Abstractions\Database\FieldEntryAbstract;

final class SelectBuilder
{

    /**
     * @var RelationshipBuilder
     */
    protected $relationshipBuilder;

    /**
     * SelectBuilder constructor.
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
     * @param array              $arguments
     * @param array              $options
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract[]|ExpressiveContract|boolean
     *
     * @throws TException
     */
    public function select(
        array $arguments,
        array $options = [],
        ExpressiveContract $model
    ) {
        if (empty($model->getSchema()->getDatabase())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $table = $model->getSchema()->getDatabase()->getTable();

        // retorna todas as dependencias atribuidas ao respectivo model
        $dependencies = ['*'];

        // retorna todas as colunas atribuidas ao respectivo model
        $columns = ['*'];

        $stmt = Capsule::table($table);
        if (!empty($arguments)) {
            foreach ($arguments as $argument) {
                $stmt->where(
                    $argument['column'],
                    !array_key_exists(
                        'operator',
                        $argument
                    ) ? '=' : $argument['operator'],
                    $argument['value'],
                    !array_key_exists(
                        'chainType',
                        $argument
                    ) ? 'and' : $argument['chainType']
                );
            }
        }

        if (!empty($options)) {
            if (array_key_exists(
                'orderBy',
                $options
            )) {
                if(count(array_filter(
                    array_keys($options['orderBy']),
                    'is_string'
                )) > 0) {
                    $options['orderBy'] = [$options['orderBy']];
                }
                foreach ($options['orderBy'] as $option) {
                    $stmt->orderBy(
                        $option['column'],
                        array_key_exists(
                            'direction',
                            $option
                        ) ? $option['direction'] : 'asc'
                    );
                }
            }

            if (array_key_exists(
                'limit',
                $options
            )) {

                if (array_key_exists(
                    'number',
                    $options['limit']
                )) {
                    $stmt->limit(
                        intval($options['limit']['number'])
                    );
                }

                if (array_key_exists(
                    'offset',
                    $options['limit']
                )) {
                    $stmt->offset(
                        intval($options['limit']['offset'])
                    );
                }
            }

            if (array_key_exists(
                'withDependencies',
                $options
            )) {
                $dependencies = !is_array($options['withDependencies']) ? [$options['withDependencies']] : $options['withDependencies'];
            }

            if (array_key_exists(
                'withProperties',
                $options
            )) {
                $withProperties = !is_array($options['withProperties']) ? [$options['withProperties']] : $options['withProperties'];

                $columns = $this->columns($model, $withProperties);
            }
        }

        try {
            $result = $stmt->get($columns)->toArray();
        } catch (\PDOException $exception) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                400
            );
        }

        if (empty($result)) {
            return false;
        }

        $class = get_class($model);
        $instances = array_map(function ($item) use ($class, $dependencies) {
            $instance = Wrapper::fetchStdClassToExpressiveNewModel($item, $class);
            if (!empty($instance)) {
                $instance = $this->searchForDependencies($instance, $dependencies);

                return $instance;
            }
        }, $result);

        return count($instances) > 1 ? $instances : $instances[0];
    }

    /**
     * @param ExpressiveContract $model
     *
     * @param boolean            $dependencies
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TException
     */
    public function search(ExpressiveContract $model, $dependencies = true)
    {
        if (empty($model->getSchema()->getDatabase())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $table = $model->getSchema()->getDatabase()->getTable();

        $primaryKeys = $model->getSchema()->getDatabase()->getPrimaryKeys();
        $stmt = Capsule::table($table);

        foreach ($primaryKeys as $key) {

            $value = $model->{$key};

            $meta = $model->getSchema()->getPropertyEntry('property', $key);
            if (empty($value) && empty($meta->getBehavior()->isRequired())) {
                return false;
            }

            if (empty($value)) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "property '{$key}' used as primary key cannot be empty at " . get_class($model) . " instance",
                    400
                );
            }

            $stmt->where(
                $key,
                '=',
                $value
            );
        }

        try {
            $result = $stmt->get()->toArray();
        } catch (\PDOException $exception) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                400
            );
        }

        if (empty($result) || count($result) > 1) {
            return false;
        }
        $instance = Wrapper::fetchStdClassToExpressiveModel(
            $result[0],
            $model
        );

        if (!empty($dependencies)) {
            $instance = $this->searchForDependencies($instance);
        }

        return $instance;
    }

    /**
     * @param array              $arguments
     * @param ExpressiveContract $model
     *
     * @return int
     *
     * @throws TException
     */
    public function count(
        array $arguments = [],
        ExpressiveContract $model
    ) {

        if (empty($model->getSchema()->getDatabase())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $table = $model->getSchema()->getDatabase()->getTable();

        $stmt = Capsule::table($table);
        if (!empty($arguments) && is_array($arguments)) {
            foreach ($arguments as $argument){
                $stmt->where(
                    $argument['column'],
                    !(array_key_exists(
                        'operator',
                        $argument
                    )) ? '=' : $argument['operator'],
                    $argument['value'],
                    !array_key_exists(
                        'chainType',
                        $argument
                    ) ? 'and' : $argument['chainType']
                );
            }
        }

        try {
            $result = $stmt->count();
        } catch (\PDOException $exception) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                $exception->getMessage(),
                500
            );
        }

        return $result;
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function last(ExpressiveContract $model)
    {
        if (empty($model->getSchema()->getDatabase())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $primaryKeys = $model->getSchema()->getDatabase()->getPrimaryKeys();

        $arguments = [];

        $options = [
            'limit'            => [
                'number' => 1
            ],
            'orderBy'          => [],
            'withDependencies' => true
        ];

        foreach ($primaryKeys as $key) {
            $value = $model->{$key};
            $meta = $model->getSchema()->getPropertyEntry(
                'property',
                $key
            );
            if (!empty($meta->getBehavior()->isAutoIncrement())) {
                $options['orderBy'][] = [
                    'column'    => $key,
                    'direction' => 'desc'
                ];
            } elseif (!empty($value)) {
                $arguments[] = [
                    'column' => $key,
                    'value'  => $value
                ];
            }
        }

        return $this->select($arguments, $options, $model);
    }

    /**
     * @param ExpressiveContract $model
     * @param array              $dependencies
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function searchForDependencies($model, $dependencies = ['*'])
    {
        $selectForAll = boolval(in_array('*', $dependencies));

        $dependencies = array_values(array_filter(
            $model->getSchema()->getDatabase()->getFields(), function (FieldEntryAbstract $field) use ($dependencies, $selectForAll) {
                $selectFor = false;
                if (!empty($field->getObject())) {
                    if (!empty($selectForAll)) {
                        return true;
                    }

                    $selectFor = boolval(in_array($field->getProperty(), $dependencies));
                }
                return $selectFor;
            }
        ));

        if (!empty($dependencies)) {
            foreach ($dependencies as $dependency) {
                if (empty($dependency->getObject()->getRelationship())) {
                    throw new TException(
                        __CLASS__,
                        __METHOD__,
                        'a dependency must have a relationship assigned to it in the schema',
                        500
                    );
                }

                $relationship = $dependency->getObject()->getRelationship()->getType();

                if (!method_exists(
                    $this->getRelationshipBuilder(),
                    $relationship
                )
                ) {
                    throw new TException(
                        __CLASS__,
                        __METHOD__,
                        "{$relationship} is not a valid relationship type at " . get_class($model),
                        400
                    );
                }

                $model = $this->getRelationshipBuilder()->{$relationship}($model, $dependency);
            }
        }

        return $model;
    }

    /**
     * @param ExpressiveContract $model
     * @param array              $withProperties
     *
     * @return array
     *
     * @throws TException
     */
    private function columns(
        $model,
        $withProperties
    ) {
        $searchFor = [];

        foreach ($withProperties as $property) {
            $meta = $model->getSchema()->getPropertyEntry('property', $property);
            if (empty($meta)) {
                new TException(
                    __CLASS__,
                    __METHOD__,
                    "property {$property} not found in schema",
                    400
                );
            }
            if (empty($meta->getObject())) {
                $searchFor[] = $property;
            }
        }

        return empty($searchFor) ? ['*'] : $searchFor;
    }
}