<?php

namespace Solis\Expressive\Classes\Illuminate;

use Solis\Expressive\Abstractions\ExpressiveAbstract;

/**
 * Class ExpressiveSlim
 *
 * @package Solis\Expressive\Classes
 */
class Expressive extends ExpressiveAbstract
{

    /**
     * Expressive constructor.
     *
     * @param string $file
     * @param string $table
     */
    protected function __construct(
        $file,
        $table
    ) {
        parent::__construct(
            $file,
            $table
        );

        $this->setDatabaseContainer(
            Wrapper::make($this->getSchema())
        );
    }
}