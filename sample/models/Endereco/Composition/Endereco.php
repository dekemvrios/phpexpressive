<?php

namespace Solis\Expressive\Sample\Endereco\Composition;

use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Endereco
 *
 * @package Solis\Expressive\Sample\Endereco\Composition
 */
class Endereco
{
    use HasMagic;

    protected $logradouro;
    protected $numero;
    protected $bairro;
    protected $cep;
    protected $cidade;
    protected $estado;

    /**
     * Pessoa constructor.
     */
    public function __construct()
    {
        $this->start(dirname(__FILE__) . '/Endereco.json');
    }
}