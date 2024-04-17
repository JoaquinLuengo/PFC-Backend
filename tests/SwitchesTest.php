<?php

namespace App\Tests\Entity;

use App\Entity\Switches;
use App\Entity\Sector;
use App\Entity\Agente;
use App\Entity\IpAdress;
use PHPUnit\Framework\TestCase;

class SwitchesTest extends TestCase
{
    public function testGetAndSetMarca()
    {
        $switches = new Switches();
        $switches->setMarca('Cisco');

        $this->assertEquals('Cisco', $switches->getMarca());
    }

    public function testGetAndSetModelo()
    {
        $switches = new Switches();
        $switches->setModelo('Catalyst 2960');

        $this->assertEquals('Catalyst 2960', $switches->getModelo());
    }

    public function testGetAndSetEstadoConexion()
    {
        $switches = new Switches();
        $switches->setEstadoConexion(true);

        $this->assertTrue($switches->isEstadoConexion());
    }

    public function testGetAndSetSector()
    {
        $switches = new Switches();
        $sector = new Sector();
        $switches->setSector($sector);

        $this->assertEquals($sector, $switches->getSector());
    }

    public function testGetAndSetAgente()
    {
        $switches = new Switches();
        $agente = new Agente();
        $switches->setAgente($agente);

        $this->assertEquals($agente, $switches->getAgente());
    }

    public function testGetAndSetEtiqueta()
    {
        $switches = new Switches();
        $switches->setEtiqueta('Switch de la sala de servidores');

        $this->assertEquals('Switch de la sala de servidores', $switches->getEtiqueta());
    }

    public function testGetAndSetIpadress()
    {
        $switches = new Switches();
        $ipAddress = new IpAdress();
        $switches->setIpadress($ipAddress);

        $this->assertEquals($ipAddress, $switches->getIpadress());
    }
}