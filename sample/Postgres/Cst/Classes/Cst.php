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
     * __construct
     */
    public function __construct()
    {
        $this->boot(dirname(__FILE__) . '/Cst.json');

        parent::__construct(
            dirname(__FILE__) . '/Cst.json',
            'tbcst',
            self::$schema
        );
    }
}