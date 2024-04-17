<?php

namespace App\Tests\Entity;

use App\Entity\Equipo;
use App\Entity\IpAdress;
use PHPUnit\Framework\TestCase;

class EquipoTest extends TestCase
{
    public function testGetAndSetNombreDispositivo()
    {
        $equipo = new Equipo();
        $equipo->setNombreDispositivo('Equipo');

        $this->assertEquals('Equipo', $equipo->getNombreDispositivo());
    }

    public function testGetAndSetSistemaOperativo()
    {
        $equipo = new Equipo();
        $equipo->setSistemaOperativo('Windows');

        $this->assertEquals('Windows', $equipo->getSistemaOperativo());
    }

    public function testGetAndSetMemoriaRam()
    {
        $equipo = new Equipo();
        $equipo->setMemoriaRam(8);

        $this->assertEquals(8, $equipo->getMemoriaRam());
    }

    public function testGetAndSetMacaddress()
    {
        $equipo = new Equipo();
        $equipo->setMacaddress('00:1A:2B:3C:4D:5E');

        $this->assertEquals('00:1A:2B:3C:4D:5E', $equipo->getMacaddress());
    }

    public function testGetAndSetIpadress()
    {
        $equipo = new Equipo();
        $ipAddress = new IpAdress();
        $equipo->setIpadress($ipAddress);

        $this->assertEquals($ipAddress, $equipo->getIpadress());
    }
}