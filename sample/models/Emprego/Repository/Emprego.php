<?php

namespace Solis\Expressive\Sample\Emprego\Repository;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Emprego
 *
 * @package Solis\Expressive\Sample\Emprego\Repository
 */
class Emprego extends Expressive
{
    use HasMagic;

    protected $ID;
    protected $cargo;

    /**
     * Pessoa constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->start(dirname(__FILE__) . '/Emprego.json');
    }
}