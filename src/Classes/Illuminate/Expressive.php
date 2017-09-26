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
     */
    protected function __construct() {
        parent::__construct();

        $this->setDatabaseContainer(
            Wrapper::make()
        );
    }
}