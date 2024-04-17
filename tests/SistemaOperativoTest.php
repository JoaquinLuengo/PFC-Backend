<?php

namespace App\Tests\Entity;

use App\Entity\SistemaOperativo;
use PHPUnit\Framework\TestCase;

class SistemaOperativoTest extends TestCase
{
    public function testGetAndSetNombre()
    {
        $sistemaOperativo = new SistemaOperativo();
        $sistemaOperativo->setNombre('Windows');

        $this->assertEquals('Windows', $sistemaOperativo->getNombre());
    }

    public function testGetAndSetVersion()
    {
        $sistemaOperativo = new SistemaOperativo();
        $sistemaOperativo->setVersion('10.0');

        $this->assertEquals('10.0', $sistemaOperativo->getVersion());
    }
}