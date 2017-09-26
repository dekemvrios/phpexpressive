<?php

namespace Solis\Expressive\Classes\Illuminate\Select;

use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;
use Solis\Expressive\Contracts\ExpressiveContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Classes\Illuminate\Wrapper;
use Solis\Expressive\Classes\Illuminate\Diglett;
use Solis\Breaker\TException;

/**
 * Class SelectBuilder
 *
 * @package Solis\Expressive\Classes\Illuminate\Select
 */
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
     * @return ExpressiveContract[]|boolean
     *
     * @throws TException
     */
    public function select(
        array $arguments,
        array $options = [],
        ExpressiveContract $model
    ) {
        if (empty($model::$schema->getRepository())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $table = $model::$schema->getRepository();

        // não retorna as dependencias atribuidas ao respectivo model
        $dependencies = false;

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
                if (count(array_filter(
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

            // Caso withDependencies for fornecida para a operação de consulta, essa somente pederá
            // assumir valores boolean ou array
            if (array_key_exists(
                'withDependencies',
                $options
            )) {
                switch ($options['withDependencies']) {
                    case is_array($options['withDependencies']):
                        $dependencies = $options['withDependencies'];
                        break;
                    case 'true':
                        $dependencies = true;
                        break;
                    case 'false':
                        $dependencies = false;
                        break;
                    case is_bool($options['withDependencies']):
                        $dependencies = $options['withDependencies'];
                        break;
                    default:
                        $dependencies = false;
                        break;
                }
            }

            if (array_key_exists(
                'withProperties',
                $options
            )) {
                $withProperties = !is_array($options['withProperties']) ?
                        [$options['withProperties']] : $options['withProperties'];

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

            if (!Diglett::toDig()) {
                return $instance;
            }

            if (!empty($instance)) {
                $instance = $this->searchForDependencies($instance, $dependencies);

                return $instance;
            }
        }, $result);

        return $instances;
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

            if (empty($value) && empty($key->getBehavior()->isRequired())) {
                return false;
            }

            if (empty($value)) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "property '{$key->getProperty()}' used as primary key cannot be empty at " .
                    get_class($model) . " instance",
                    400
                );
            }

            $stmt->where(
                $key->getProperty(),
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

        if (!Diglett::toDig()) {
            return $instance;
        }

        if (!empty($dependencies)) {
            $instance = $this->searchForDependencies($instance, true);
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

        if (empty($model::$schema->getRepository())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $table = $model::$schema->getRepository();

        $stmt = Capsule::table($table);
        if (!empty($arguments) && is_array($arguments)) {
            foreach ($arguments as $argument) {
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
     * @param boolean $dependencies
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function last(ExpressiveContract $model, $dependencies = true)
    {
        if (empty($model::$schema->getRepository())) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database schema entry has not been defined for ' . get_class($model),
                400
            );
        }

        $arguments = [];
        $options = [
            'limit'            => [
                'number' => 1,
            ],
            'orderBy'          => [],
            'withDependencies' => $dependencies,
        ];

        foreach ($model::$schema->getKeys() as $key) {
            // Se uma chave possuir comportamento auto incremental, então o consumidor não é
            // responsável por sua atribuição, desse modo ela será utilizada como filtro de
            // ordenação para a operação de consulta.
            if (!empty($key->getBehavior()->isAutoIncrement())) {
                $options['orderBy'][] = [
                    'column'    => $key->getField(),
                    'direction' => 'desc',
                ];

                continue;
            }

            $value = $model->{$key->getProperty()};

            // Caso houver valor atribuido ao model em uma propriedade definida como chave
            // essa será atribuida como filtro de consulta, permitindo situaçpes em que o
            // registro possui chave composta.
            if (!empty($value)) {
                $arguments[] = [
                    'column' => $key->getField(),
                    'value'  => $value,
                ];
            }
        }

        $select = $this->select(
            $arguments,
            $options,
            $model
        );

        if (is_array($select) && !empty($select)) {
            return $select[0];
        }

        return $select;
    }

    /**
     * @param ExpressiveContract $model
     * @param array|boolean      $dependenciItems
     *
     * @return ExpressiveContract
     *
     * @throws TException
     */
    public function searchForDependencies(
        $model,
        $dependenciItems
    ) {
        // O valor atribuido a propriedade $dependenciItems poderá assumir valor valor
        // boolean ou array.
        //
        // Caso boolean e TRUE, todas as dependências serão retornadas, caso boolean e
        // FALSE, nenhuma dependéncia será retornada pela consulta.
        //
        // Caso array, esse deverá conter o nome das dependencias vinculadas ao registro
        // que serão retornadas pela consulta. Se array vazio, nenhuma dependência será
        // retornada pela operação.
        if (empty($dependenciItems)) {
            return $model;
        }

        $dependencies = $model::$schema->getDependencies();
        if (empty($dependencies)) {
            return $model;
        }

        // se array, filtra as dependencias a serem retornadas somente se no conjunto de
        // entradas desejadas para consulta.
        if (is_array($dependenciItems)) {
            $dependencies = array_filter($dependencies, function (PropertyContract $property) use ($dependenciItems) {
                return in_array($property->getProperty(), $dependenciItems);
            });
        }

        if (empty($dependencies)) {
            return $model;
        }

        foreach ($dependencies as $dependency) {
            if (empty($dependency->getComposition()->getRelationship())) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    'a dependency must have a relationship assigned to it in the schema',
                    500
                );
            }
            // chama o método de consulta de acordo com os tipos de relacionamento
            // válidos atribuidos a entrada no schema vinculado ao registro.
            $relationship = $dependency->getComposition()->getRelationship()->getType();
            $model = $this->getRelationshipBuilder()->{$relationship}(
                $model,
                $dependency
            );
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
        $searchFor = ['*'];

        // caso fornecido o array withProperties, essa deverá conter a relação de propriedades
        // do registro que serão retornados pela consulta. Em caso de relacionamento, considera
        // apenas relacionamento do tipo hasOne. Caso vazio, retorna todas as propriedades.
        //
        // Vale notar que, caso utilizado em conjunto com withDependencies, onde uma propriedade
        // representar um relacionamento hasOne, se essa não estiver relacionada no conjunto de
        // propriedades, essa não será exibida. E, em caso de relacionamento hasMany, essa também
        // não será retornada caso os campos que compoe o relacionamento não forem também listados
        if (!empty($withProperties)) {
            $columns = $model::$schema->getSearchableFieldsString();

            $searchFor = array_values(array_filter($columns, function ($field) use ($withProperties) {
                return in_array($field, $withProperties);
            }));
        }

        return !empty($searchFor) ? $searchFor : ['*'];
    }
}
