<?php

namespace Solis\Expressive\Sample\Pessoa\Repository;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Pessoa
 *
 * @package Solis\Expressive\Sample\Pessoa\Repository
 */
class Pessoa extends Expressive
{
    use HasMagic;

    protected $ID;
    protected $codigo;
    protected $nome;
    protected $cidade;
    protected $inscricaoFederal;

    /**
     * Pessoa constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->start(dirname(__FILE__) . '/Pessoa.json');
    }
}