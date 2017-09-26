<?php

namespace Solis\Expressive\Test\Fixtures\IntegrationTest;

use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class JsonEndereco
 *
 * @package Solis\Expressive\Test\Fixtures\IntegrationTest
 */
class JsonEndereco
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
        $this->start(dirname(__FILE__) . '/JsonEndereco.json');
    }
}
