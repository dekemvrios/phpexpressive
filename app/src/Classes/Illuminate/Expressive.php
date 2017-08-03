<?php

namespace Solis\Expressive\Classes\Illuminate;

use Solis\Expressive\Abstractions\ExpressiveAbstract;
use Solis\Expressive\Schema\Contracts\SchemaContract;

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
     * @param string         $file
     * @param string         $table
     * @param SchemaContract $schema
     */
    protected function __construct(
        $file,
        $table,
        $schema
    ) {
        parent::__construct(
            $file,
            $table,
            $schema
        );

        $this->setDatabaseContainer(
            Wrapper::make($this->getSchema())
        );
    }
}