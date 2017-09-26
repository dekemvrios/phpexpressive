<?php

namespace Solis\Expressive\Classes\Illuminate;

use Solis\Expressive\Abstractions\ExpressiveAbstract;

/**
 * Class Expressive
 *
 * @package Solis\Expressive\Classes\Illuminate
 */
class Expressive extends ExpressiveAbstract
{

    /**
     * Expressive constructor.
     */
    protected function __construct()
    {
        parent::__construct();

        $this->setDatabaseContainer(
            Wrapper::make()
        );
    }
}
