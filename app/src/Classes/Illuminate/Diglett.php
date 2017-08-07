<?php

namespace Solis\Expressive\Classes\Illuminate;

use Solis\Breaker\TException;

/**
 * Class Diglett
 *
 * @package Solis\Expressive\Classes\Illuminate
 */
class Diglett
{

    /**
     * @var boolean
     */
    public static $isActive;

    /**
     * @var int
     */
    public static $levels;

    /**
     * @param int $levels
     *
     * @throws TException
     */
    public static function enable(
        $levels = 1
    ) {
        if (isset(self::$isActive)) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'Diglett is already active',
                500
            );
        }

        if ($levels < 1) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'level value cannot be less than 1',
                500
            );
        }

        self::$isActive = true;

        self::$levels = $levels;
    }

    /**
     * toDig
     *
     * @return bool
     */
    public static function toDig()
    {
        if (!isset(self::$isActive)) {
            return true;
        }

        self::$levels -= 1;

        if (self::$levels < 0) {
            return false;
        }

        return true;
    }

    /**
     * @throws TException
     */
    public static function disable()
    {
        if (isset(self::$isActive)) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'Diglett is not active',
                500
            );
        }

        self::$isActive = false;

        self::$levels = 0;
    }
}