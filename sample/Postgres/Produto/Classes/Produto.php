<?php

namespace Sample\Postgres\Produto\Classes;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Produto
 *
 * @package Sample\Postgres\Produto\Classes
 */
class Produto extends Expressive
{
    use HasMagic;

    protected $empcodigo;
    protected $procodigo;
    protected $prodescricao;
    protected $giccodigo;
    protected $gpccodigo;
    protected $cstipicodigo;

    /**
     * Produto constructor.
     *
     */
    public function __construct()
    {
        $this->boot(dirname(__FILE__) . '/Produto.json');

        parent::__construct(
            dirname(__FILE__) . '/Produto.json',
            'tbproduto',
            self::$schema
        );
    }
}
