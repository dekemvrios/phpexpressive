<?php

namespace Solis\Expressive\Classes\Illuminate\Query;

use Illuminate\Database\Capsule\Manager as Capsule;
use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Expressive\Exception;
use Solis\Expressive\Schema\Contracts\Entries\Property\PropertyContract;

/**
 * Class SearchStmt
 *
 * @package Solis\Expressive\Classes\Illuminate\Query
 */
class SearchStmt
{
    /**
     * @var \Illuminate\Database\Query\Builder $stmt
     */
    private $stmt;

    /**
     * @var ExpressiveContract
     */
    private $model;

    /**
     * SearchStmt constructor.
     *
     * @param ExpressiveContract $model
     */
    public function __construct(ExpressiveContract $model)
    {
        $this->stmt = Capsule::table($model->getSchema()->getRepository());

        $this->model = $model;
    }

    /**
     * @return \StdClass|bool
     */
    public function search()
    {
        if (!$this->setWhere()) {
            return false;
        }
        $result = $this->stmt->get()->toArray();

        return $this->isValidSearchResult($result) ? $result[0] : false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function setWhere()
    {
        $model = $this->model;

        return $this->setWhereKeys($model);
    }

    /**
     * @param ExpressiveContract $model
     *
     * @return bool
     * @throws Exception
     */
    private function setWhereKeys(ExpressiveContract $model): bool
    {
        /**
         * @var PropertyContract[] $primaryKeys
         */
        $primaryKeys = $model->getSchema()->getKeys();

        foreach ($primaryKeys as $key) {
            $value = $model->{$key->getProperty()};

            if (is_null($value) && !$key->getBehavior()->isRequired()) {
                return false;
            }

            if (is_null($value)) {
                throw new Exception("property '{$key->getProperty()}' used as primary key cannot be null", 400);
            }

            $this->stmt->where($key->getProperty(), '=', $value);
        }

        return true;
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
}
