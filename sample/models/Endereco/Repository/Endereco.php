<?php

namespace Solis\Expressive\Sample\Endereco\Repository;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Pessoa
 *
 * @package Solis\Expressive\Sample\Endereco\Repository
 */
class Endereco extends Expressive
{
    use HasMagic;

    protected $ID;
    protected $pessoaID;
    protected $logradouro;
    protected $bairro;
    protected $cep;
    protected $cidade;

    /**
     * Pessoa constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->start(dirname(__FILE__) . '/Endereco.json');
    }
}