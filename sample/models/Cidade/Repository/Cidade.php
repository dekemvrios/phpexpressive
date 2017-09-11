<?php

namespace Solis\Expressive\Sample\Cidade\Repository;

use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Cidade
 *
 * @package Solis\Expressive\Sample\Cidade\Repository
 */
class Cidade
{

    use HasMagic;

    protected $ID;
    protected $nome;
    protected $ibgeId;

    /**
     * Cidade constructor.
     */
    public function __construct()
    {
        $this->start(dirname(__FILE__) . '/Cidade.json');
    }
}