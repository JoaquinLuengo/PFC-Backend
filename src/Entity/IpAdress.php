<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\IpAdressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IpAdressRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['ipaddress.read']],
    denormalizationContext: ['groups' => ['ipaddress.write']]
)]
class IpAdress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ipaddress.read','ipaddress.write','agente.read','agente.write','equipo.read','equipo.write','switches.read','switches.write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['ipaddress.read','ipaddress.write','agente.read','agente.write','equipo.read','equipo.write','switches.read','switches.write'])]
    private ?string $direccion = null;

    #[ORM\OneToOne(inversedBy: 'ipadress', cascade: ['persist', 'remove'])]
    #[Groups(['ipaddress.read','ipaddress.write'])]
    private ?Agente $agente = null;

    #[ORM\OneToOne(inversedBy: 'ipadress', cascade: ['persist', 'remove'])]
    #[Groups(['ipaddress.read','ipaddress.write'])]
    private ?Switches $switches = null;

   // #[ORM\OneToOne]
    #[ORM\OneToOne(inversedBy: 'ipadress', cascade: ['persist', 'remove'])]
    #[Groups(['ipaddress.read','ipaddress.write'])]
    private ?Equipo $equipo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getAgente(): ?Agente
    {
        return $this->agente;
    }

    public function setAgente(?Agente $agente): self
    {
        $this->agente = $agente;

        return $this;
    }

    public function getSwitches(): ?Switches
    {
        return $this->switches;
    }

    public function setSwitches(?Switches $switches): self
    {
        $this->switches = $switches;

        return $this;
    }

    public function getEquipo(): ?Equipo
    {
        return $this->equipo;
    }

    public function setEquipo(?Equipo $equipo): self
    {
        $this->equipo = $equipo;

        return $this;
    }

}
