<?php

namespace App\Tests\Entity;

use App\Entity\IpAdress;
use App\Entity\Agente;
use App\Entity\Switches;
use App\Entity\Equipo;
use PHPUnit\Framework\TestCase;

class IpAdressTest extends TestCase
{
    public function testGetAndSetDireccion()
    {
        $ipAddress = new IpAdress();
        $ipAddress->setDireccion('192.168.1.1');

        $this->assertEquals('192.168.1.1', $ipAddress->getDireccion());
    }

    public function testGetAndSetAgente()
    {
        $ipAddress = new IpAdress();
        $agente = new Agente();
        $ipAddress->setAgente($agente);

        $this->assertEquals($agente, $ipAddress->getAgente());
    }

    public function testGetAndSetSwitches()
    {
        $ipAddress = new IpAdress();
        $switches = new Switches();
        $ipAddress->setSwitches($switches);

        $this->assertEquals($switches, $ipAddress->getSwitches());
    }

    public function testGetAndSetEquipo()
    {
        $ipAddress = new IpAdress();
        $equipo = new Equipo();
        $ipAddress->setEquipo($equipo);

        $this->assertEquals($equipo, $ipAddress->getEquipo());
    }
}