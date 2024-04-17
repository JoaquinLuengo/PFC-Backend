<?php

namespace App\Tests\Entity;

use App\Entity\Sector;
use PHPUnit\Framework\TestCase;

class SectorTest extends TestCase
{
    public function testGetAndSetNombre()
    {
        $sector = new Sector();
        $sector->setNombre('Departamento de TI');

        $this->assertEquals('Departamento de TI', $sector->getNombre());
    }
}