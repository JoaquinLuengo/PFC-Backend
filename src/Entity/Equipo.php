<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EquipoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EquipoRepository::class)]
#[ApiResource(
    
    normalizationContext: ['groups' => ['equipo.read']],
    denormalizationContext: ['groups' => ['equipo.write']]
)]
class Equipo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipo.read','equipo.write','ipaddress.read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['equipo.read','equipo.write','ipaddress.read'])]
    private ?string $nombreDispositivo = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['equipo.read','equipo.write','ipaddress.read'])]
    private ?string $sistemaOperativo = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['equipo.read','equipo.write','ipaddress.read'])]
    private ?int $memoriaRam = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['equipo.read','equipo.write','ipaddress.read'])]
    private ?string $macaddress = null;

    #[Groups(['equipo.read','equipo.write'])]
    #[ORM\OneToOne(mappedBy: 'equipo', cascade: ['persist', 'remove'])]
    private ?IpAdress $ipadress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreDispositivo(): ?string
    {
        return $this->nombreDispositivo;
    }

    public function setNombreDispositivo(?string $nombreDispositivo): self
    {
        $this->nombreDispositivo = $nombreDispositivo;

        return $this;
    }

    public function getSistemaOperativo(): ?string
    {
        return $this->sistemaOperativo;
    }

    public function setSistemaOperativo(?string $sistemaOperativo): self
    {
        $this->sistemaOperativo = $sistemaOperativo;

        return $this;
    }

    public function getMemoriaRam(): ?int
    {
        return $this->memoriaRam;
    }

    public function setMemoriaRam(?int $memoriaRam): self
    {
        $this->memoriaRam = $memoriaRam;

        return $this;
    }

    public function getMacaddress(): ?string
    {
        return $this->macaddress;
    }

    public function setMacaddress(?string $macaddress): self
    {
        $this->macaddress = $macaddress;

        return $this;
    }

    public function getIpadress(): ?IpAdress
    {
        return $this->ipadress;
    }

    public function setIpadress(?IpAdress $ipadress): self
    {
        // unset the owning side of the relation if necessary
        if ($ipadress === null && $this->ipadress !== null) {
            $this->ipadress->setEquipo(null);
        }

        // set the owning side of the relation if necessary
        if ($ipadress !== null && $ipadress->getEquipo() !== $this) {
            $ipadress->setEquipo($this);
        }

        $this->ipadress = $ipadress;

        return $this;
    }

}
