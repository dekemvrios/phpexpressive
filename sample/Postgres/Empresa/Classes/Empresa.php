<?php

namespace Sample\Postgres\Empresa\Classes;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Empresa
 *
 * @package Sample\Postgres\Empresa\Classes
 */
class Empresa extends Expressive
{
    use HasMagic;

    protected $empcodigo;
    protected $empnome;
    protected $produtos;

    /**
     * Empresa constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->start(dirname(__FILE__) . '/Empresa.json');
    }
}