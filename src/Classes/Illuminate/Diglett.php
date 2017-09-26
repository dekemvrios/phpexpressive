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
    private static $isActive = false;

    /**
     * @var int
     */
    private static $levels = 1;

    /**
     * @param int $levels
     *
     * @throws TException
     */
    public static function enable(
        $levels = 1
    ) {
        self::setActive(true);
        self::setLevels($levels);
    }

    /**
     * @throws TException
     */
    public static function disable()
    {
        self::setActive(false);
        self::setLevels(1);
    }

    public static function toDig(): bool
    {
        if (!self::$isActive) {
            return true;
        }

        $canDig = self::canDig();

        self::decrementLevels();

        return $canDig;
    }

    protected static function setActive(bool $active)
    {
        if ($active && self::$isActive) {
            throw new TException(
                __CLASS__,
                __METHOD__,
                'Diglett is already active',
                500
            );
        }

        self::$isActive = $active;
    }

    protected static function setLevels(int $levels)
    {
        self::$levels = $levels;
    }

    protected static function decrementLevels()
    {
        self::$levels -= 1;
    }

    protected static function canDig(): bool
    {
        return self::$levels < 1 ? false : true;
    }
}
