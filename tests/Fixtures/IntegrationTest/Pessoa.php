<?php

namespace Solis\Expressive\Test\Fixtures\IntegrationTest;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Pessoa
 *
 * @package Solis\Expressive\Test\Fixtures\IntegrationTest
 */
class Pessoa extends Expressive
{
    use HasMagic;

    protected $ID;
    protected $nome;
    protected $inscricaoFederal;
    protected $tipo;
    protected $situacao;
    protected $endereco;
    protected $enderecoJson;

    /**
     * Pessoa constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->start(dirname(__FILE__) . '/Pessoa.json');
    }
}
