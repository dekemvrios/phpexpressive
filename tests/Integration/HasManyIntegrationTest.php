<?php

namespace Solis\Expressive\Test\Integration;

use Solis\Expressive\Test\Fixtures\IntegrationTest\Pessoa;
use Solis\Expressive\Exception;

class HasManyIntegrationTest extends AbstractIntegrationTest
{

    public function getHasMany($times = 1)
    {
        $records = [];

        for ($i = 0; $i < $times; $i++) {
            $records[] = [
                "proLogradouro" => "Rua - " . uniqid(rand()),
                "proCidade"     => "Cidade - " . uniqid(rand()),
                "proEstado"     => uniqid(rand()),
            ];
        }

        return $records;
    }

    public function testCanCreateWithOneHasMany()
    {
        Pessoa::make([
            "proNome"     => 'Fulano - ' . uniqid(rand()),
            "proEndereco" => $this->getHasMany(),
        ])->create();

        $last = Pessoa::make()->last();

        $endereco = $last->endereco;
        $this->assertNotInternalType('null', $endereco);
    }

    public function testCanCreateWithAnyHasMany()
    {
        $dependencies = rand(1, 10);
        Pessoa::make([
            "proNome"     => 'Fulano - ' . uniqid(rand()),
            "proEndereco" => $this->getHasMany($dependencies),
        ])->create();

        $last = Pessoa::make()->last();

        $endereco = !is_array($last->endereco) ? [$last->endereco] : $last->endereco;
        $this->assertCount($dependencies, $endereco);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateWithHasManyMustThrownExceptionForMissingValue()
    {
        Pessoa::make([
            "proNome"     => 'Fulano - ' . uniqid(rand()),
            "proEndereco" => [
                "proLogradouro" => "Rua - " . uniqid(rand()),
                "proEstado"     => uniqid(rand()),
            ],
        ])->create();
    }
}
