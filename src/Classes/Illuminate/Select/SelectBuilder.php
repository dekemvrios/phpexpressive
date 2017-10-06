<?php

namespace Solis\Expressive\Classes\Illuminate\Select;

use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Expressive\Classes\Illuminate\Diglett;
use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Expressive\Classes\Illuminate\Query\Builder;
use Solis\Expressive\Exception;

/**
 * Class SelectBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Select
 */
class SelectBuilder
{

    /**
     * @var RelationshipBuilder
     */
    private $relationshipBuilder;

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
     * @param ExpressiveContract $model
     * @param array              $arguments
     * @param array              $options
     *
     * @return ExpressiveContract[]|boolean
     *
     * @throws TExceptionAbstract
     */
    public function select(
        ExpressiveContract $model,
        array $arguments,
        array $options = []
    ) {
        $table = $model::$schema->getRepository();

        $Builder = new Builder($table, $arguments, $options);

        $stmt = $Builder->whereArguments()->orderBy()->limit()->getStmt();

        try {
            $result = $stmt->get($this->columns($model, $options))->toArray();
        } catch (\PDOException $exception) {
            throw new Exception($exception->getMessage(), 500);
        }

        return $this->parseSelectResult($model, $result, $Builder);
    }

    /**
     * Caso fornecido o array withProperties, essa deverá conter a relação de propriedades
     * do registro que serão retornados pela consulta. Em caso de relacionamento, considera
     * apenas relacionamento do tipo hasOne. Caso vazio, retorna todas as propriedades.
     *
     * Vale notar que, caso utilizado em conjunto com withDependencies, onde uma propriedade
     * representar um relacionamento hasOne, se essa não estiver relacionada no conjunto de
     * propriedades, essa não será exibida. E, em caso de relacionamento hasMany, essa também
     * não será retornada caso os campos que compoe o relacionamento não forem também listados
     *
     * @param ExpressiveContract $model
     * @param array              $options
     *
     * @return array
     *
     * @throws TExceptionAbstract
     */
    private function columns(
        $model,
        $options
    ) {
        $withProperties = $this->sanitizeArrayInput($options['withProperties'] ?? []);

        return $this->getSelectableFields($model, $withProperties);
    }

    /**
     * @param ExpressiveContract $model
     * @param array              $rows
     * @param mixed              $dependencies
     *
     * @return array
     */
    protected function fetchRelationships(ExpressiveContract $model, array $rows, $dependencies)
    {
        $instances = array_map(function ($item) use ($model, $dependencies) {

            $instance = $this->makeNewExpressiveModel($item, $model);

            return !Diglett::toDig() ? $instance : $this->getModelRelationships($instance, $dependencies);
        }, $rows);

        return $instances;
    }

    /**
     * @param ExpressiveContract $model
     *
     * @param boolean            $dependencies
     *
     * @return ExpressiveContract|boolean
     *
     * @throws TExceptionAbstract
     */
    public function search(ExpressiveContract $model, $dependencies = true)
    {
        $table = $model::$schema->getRepository();

        $Builder = new Builder($table);

        $stmt = $Builder->whereKeys($model)->getStmt();

        try {
            $result = $stmt->get()->toArray();
        } catch (\PDOException $exception) {
            throw new Exception($exception->getMessage(), 500);
        }

        if (!$this->isValidSearchResult($result)) {
            return false;
        }

        $instance = $this->makeNewExpressiveModel($result[0], $model);

        return $this->parseSearchResult($dependencies, $instance);
    }

    /**
     * @param array              $arguments
     * @param ExpressiveContract $model
     *
     * @return int
     *
     * @throws TExceptionAbstract
     */
    public function count(
        ExpressiveContract $model,
        array $arguments = []
    ) {
        $table = $model::$schema->getRepository();

        $Builder = new Builder($table, $arguments);

        $stmt = $Builder->whereArguments()->getStmt();

        try {
            $result = $stmt->count();
        } catch (\PDOException $exception) {
            throw new Exception($exception->getMessage(), 500);
        }

        return $result;
    }

    /**
     * Se uma chave possuir comportamento auto incremental, então o consumidor não é
     * responsável por sua atribuição, desse modo ela será utilizada como filtro de
     * ordenação para a operação de consulta.
     *
     * Caso houver valor atribuido ao model em uma propriedade definida como chave
     * essa será atribuida como filtro de consulta, permitindo situacoes em que o
     * registro possui chave composta.
     *
     * @param ExpressiveContract $model
     * @param boolean $dependencies
     *
     * @return ExpressiveContract|bool
     *
     * @throws TExceptionAbstract
     */
    public function last(ExpressiveContract $model, $dependencies = true)
    {

        $options = $this->getOptionsForLastStmt($dependencies);

        $arguments = [];
        foreach ($model::$schema->getKeys() as $key) {
            if ($key->getBehavior()->isAutoIncrement()) {
                $options['orderBy'][] = [
                    'column'    => $key->getField(),
                    'direction' => 'desc',
                ];

                continue;
            }

            $value = $model->{$key->getProperty()};
            if ($value) {
                $arguments[] = [
                    'column' => $key->getField(),
                    'value'  => $value,
                ];
            }
        }

        $select = $this->select($model, $arguments, $options);

        return $select[0] ?? $select;
    }

    /**
     *
     * Considerando o parâmetro $dependenciItems, Caso boolean e TRUE, todas as dependências serão retornadas, caso
     * boolean e FALSE, nenhuma dependência será retornada pela consulta.
     *
     * Caso array, esse deverá conter o nome das dependencias vinculadas ao registro que serão retornadas pela consulta.
     * Se array vazio, nenhuma dependência será retornada pela operação.
     *
     * @param ExpressiveContract $model
     * @param array|boolean      $dependencyItems
     *
     * @return ExpressiveContract
     *
     * @throws TExceptionAbstract
     */
    public function getModelRelationships(
        $model,
        $dependencyItems
    ) {
        if (!$dependencyItems) {
            return $model;
        }

        $dependencies = $model::$schema->getDependencies();
        if (!$dependencies) {
            return $model;
        }

        if (is_array($dependencyItems)) {
            $dependencies = $this->getSearchableDependencies($dependencyItems, $dependencies);

            if (!$dependencies) {
                return $model;
            }
        }

        foreach ($dependencies as $dependency) {
            if (empty($dependency->getComposition()->getRelationship())) {
                throw new Exception('a dependency must have a relationship schema entry for database operations', 400);
            }

            $model = $this->getRelationshipByType($model, $dependency);
        }

        return $model;
    }

    /**
     * @param $model
     * @param $withProperties
     *
     * @return array
     */
    private function getSelectableFields($model, $withProperties): array
    {
        $columns = $model::$schema->getSearchableFieldsString();

        $searchFor = array_values(array_filter($columns, function ($field) use ($withProperties) {
            return in_array($field, $withProperties);
        }));

        return $searchFor ?: ['*'];
    }

    /**
     * @param $input
     *
     * @return array
     */
    private function sanitizeArrayInput($input): array
    {
        return !is_array($input) ? [$input] : $input;
    }

    /**
     * @param ExpressiveContract $model
     * @param array              $result
     * @param Builder            $Builder
     *
     * @return array
     */
    private function parseSelectResult(ExpressiveContract $model, $result, $Builder): array
    {
        return empty($result) ? $result : $this->fetchRelationships($model, $result, $Builder->dependencies());
    }

    /**
     * @param boolean            $dependencies
     * @param ExpressiveContract $instance
     *
     * @return ExpressiveContract
     */
    private function parseSearchResult($dependencies, $instance): ExpressiveContract
    {
        return !Diglett::toDig() || !$dependencies ? $instance : $this->getModelRelationships($instance, true);
    }

    /**
     * @param $result
     *
     * @return bool
     */
    private function isValidSearchResult($result): bool
    {
        return $result && count($result) === 1;
    }

    /**
     * @param boolean $dependencies
     *
     * @return array
     */
    private function getOptionsForLastStmt($dependencies): array
    {
        $options = [
            'limit'            => [
                'number' => 1,
            ],
            'orderBy'          => [],
            'withDependencies' => $dependencies,
        ];

        return $options;
    }

    /**
     * @param array              $dependencyItems
     * @param PropertyContract[] $dependencies
     *
     * @return array
     */
    private function getSearchableDependencies($dependencyItems, $dependencies): array
    {
        $dependencies = array_filter($dependencies, function (PropertyContract $property) use ($dependencyItems) {
            return in_array($property->getProperty(), $dependencyItems);
        });

        return $dependencies;
    }

    /**
     * @param ExpressiveContract $model
     * @param PropertyContract   $dependency
     *
     * @return ExpressiveContract
     */
    private function getRelationshipByType(ExpressiveContract $model, PropertyContract $dependency)
    {
        $relationship = $dependency->getComposition()->getRelationship()->getType();

        $model = $this->getRelationshipBuilder()->{$relationship}($model, $dependency);

        return $model;
    }

    /**
     * @param \stdClass $stdClass
     * @param           $class
     *
     * @return mixed
     */
    public function makeNewExpressiveModel(\stdClass $stdClass, $class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return call_user_func_array([$class, 'make'], [(array)$stdClass]);
    }
}
