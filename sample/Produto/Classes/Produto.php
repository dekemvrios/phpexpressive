<?php

namespace Sample\Produto\Classes;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Produto
 *
 * @package Sample\Produto\Classes
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
     */
    public function __construct()
    {
        parent::__construct();

        $this->start(dirname(__FILE__) . '/Produto.json');
    }
}
