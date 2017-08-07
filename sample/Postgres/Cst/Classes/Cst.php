<?php

namespace Sample\Postgres\Cst\Classes;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Cst
 *
 * @package Sample\Postgres\Cst\Classes
 */
class Cst extends Expressive
{
    use HasMagic;

    protected $cstcodigo;
    protected $csttipo;
    protected $cstcst;
    protected $cstdescricao;
    protected $cstsubstituicaotributaria;

    /**
     * NFe constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->start(dirname(__FILE__) . '/Cst.json');
    }
}