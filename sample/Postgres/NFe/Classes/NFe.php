<?php

namespace Sample\Postgres\NFe\Classes;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Empresa
 *
 * @package Sample\Postgres\Empresa\Classes
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
     * Cst constructor.
     *
     */
    public function __construct()
    {
        $this->boot(dirname(__FILE__) . '/NFe.json');

        parent::__construct(
            dirname(__FILE__) . '/NFe.json',
            'tbempresa',
            self::$schema
        );
    }
}