<?php

namespace App\Tests;

use App\Entity\Agente;
use App\Entity\Sector;
use App\Entity\IpAdress;
use PHPUnit\Framework\TestCase;

class AgenteTest extends TestCase
{
    public function testGetAndSetNombre(): void
    {
        $agente = new Agente();
        $agente->setNombre('Nombre');

        $this->assertEquals('Nombre', $agente->getNombre());
    }

    public function testGetAndSetApellido(): void
    {
        $agente = new Agente();
        $agente->setApellido('Apellido');

        $this->assertEquals('Apellido', $agente->getApellido());
    }

    public function testGetAndSetSector(): void
    {
        $agente = new Agente();
        
        $sector = new Sector();
        $agente->setSector($sector);

        $this->assertEquals($sector, $agente->getSector());
    }

    public function testGetAndSetIpadress(): void
    {
        $agente = new Agente();
        $ipAddress = new IpAdress();
        $agente->setIpadress($ipAddress);

        $this->assertEquals($ipAddress, $agente->getIpadress());
    }
}
