<?php

namespace Sample\Postgres\NFe\Classes;

use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Empresa
 *
 * @package Sample\Postgres\Empresa\Classes
 */
class NFeItem extends Expressive
{
    use HasMagic;

    protected $itnsequencia;
    protected $empcodigo;
    protected $nfesequencia;
    protected $itndescricao;
    protected $itnquantidade;
    protected $itnvalorunitario;
    protected $itnvalortotal;
    protected $itnncmdescricao;
    protected $itncstdescricao;
    protected $itnunmedidadescricao;

    /**
     * Cst constructor.
     *
     */
    public function __construct()
    {
        $this->boot(dirname(__FILE__) . '/NFeItem.json');

        parent::__construct(
            dirname(__FILE__) . '/NFeItem.json',
            'tbempresa',
            self::$schema
        );
    }
}