<?php

namespace Solis\Expressive\Classes\Illuminate;

use Solis\Expressive\Contracts\ExpressiveContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Solis\Breaker\TException;

/**
 * Class Database
 *
 * @package Solis\Expressive\Classes\Illuminate
 */
final class Database
{
    /**
     * @var boolean
     */
    public static $hasActiveTransaction;

    /**
     * @var string
     */
    public static $owner;

    /**
     * @param array $params
     *
     * @throws TException
     */
    public static function boot($params = [])
    {
        if (empty($params)) {
            global $aConfig;

            if (!array_key_exists(
                'db',
                $aConfig
            )
            ) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    'database params has not been defined',
                    400
                );
            }

            $params = $aConfig['db'];
        }

        if (empty($params)) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'database params has not been defined',
                400
            );
        }

        $capsule = new Capsule;

        // Set connection definition
        $capsule->addConnection(
            array_merge(
                $params,
                [
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix'    => ''
                ]
            )
        );

        $capsule->setEventDispatcher(new Dispatcher(new Container));

        $capsule->setAsGlobal();

        $capsule->bootEloquent();
    }

    /**
     * beginTransaction
     *
     * @param ExpressiveContract $model
     */
    public static function beginTransaction($model)
    {
        if (!isset(self::$hasActiveTransaction)) {
            Capsule::connection()->beginTransaction();

            self::$hasActiveTransaction = true;

            self::$owner = $model->getUniqid();
        }
    }

    /**
     * commitActiveTransaction
     *
     * @param ExpressiveContract $model
     */
    public static function commitActiveTransaction($model)
    {
        if (isset(self::$hasActiveTransaction)) {
            if ($model->getUniqid() === self::$owner) {

                Capsule::connection()->commit();

                self::$hasActiveTransaction = false;
            }
        }
    }

    /**
     * rollbackActiveTransaction
     *
     * @param ExpressiveContract $model
     */
    public static function rollbackActiveTransaction($model)
    {
        if (isset(self::$hasActiveTransaction)) {
            if ($model->getUniqid() === self::$owner) {
                Capsule::connection()->rollBack();

                self::$hasActiveTransaction = false;
            }
        }
    }
}
