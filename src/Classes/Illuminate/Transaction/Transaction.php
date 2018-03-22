<?php

namespace Solis\Expressive\Classes\Illuminate\Transaction;

use Solis\Expressive\Abstractions\ExpressiveAbstract;
use Solis\Expressive\Classes\Illuminate\Database;
use Solis\Expressive\Exception;

class Transaction
{
    /**
     * @var ExpressiveAbstract
     */
    private $model;

    /**
     * Transaction constructor.
     */
    public function __construct()
    {
        $this->model = TModel::make();
    }

    /**
     * @return $this
     */
    public function begin()
    {
        Database::beginTransaction($this->model);

        return $this;
    }

    /**
     * @return $this
     */
    public function commit()
    {
        Database::commitActiveTransaction($this->model);

        return $this;
    }

    /**
     * @return $this
     */
    public function rollback()
    {
        Database::rollbackActiveTransaction($this->model);

        return $this;
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     * @throws Exception
     */
    public static function execute(\Closure $closure)
    {
        $transaction = (new self())->begin();

        try {
            $result = $closure();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();

            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $result;
    }
}
