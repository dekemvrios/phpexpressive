<?php

namespace Sample\NFe\Classes;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Empresa
 *
 * @package Sample\Empresa\Classes
 */
class NFe extends Expressive
{
    use HasMagic;

    protected $nfesequencia;
    protected $empcodigo;
    protected $nfeserie;
    protected $nfenumero;
    protected $itemnfe;
    protected $filcodigo;
    protected $nfemodelo;
    protected $nfefinalidade;
    protected $nfesituacao;
    protected $nfeformaemissao;
    protected $nfedataemi;
    protected $nfehoraemi;
    protected $nfetipodocumento;
    protected $nfetipopagamento;
    protected $nfeconsumidorfinal;
    protected $nfeindicadorpresenca;
    protected $nfedestinooperacao;
    protected $nfetipoambiente;
    protected $natcodigo;
    protected $cidcodigo;
    protected $pescodigocliente;
    protected $nfevalortotal;
    protected $nfeprodvalortotal;
    protected $nfevalortotaldescontos;
    protected $nfemodalidadefrete;

    /**
     * NFe constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->start(dirname(__FILE__) . '/NFe.json');
    }
}