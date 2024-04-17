<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SwitchesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: SwitchesRepository::class)]

#[ApiResource(
    normalizationContext: ['groups' => ['switches.read']],
    denormalizationContext: ['groups' => ['switches.write']]
)]

class Switches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['switches.read','switches.write','ipaddress.read'])]
    private ?int $id = null;

    #[Assert\NotBlank,]
    #[Groups(['switches.read','switches.write','ipaddress.read'])]
    #[ORM\Column(length: 255)]
    private ?string $marca = null;

    #[Assert\NotBlank]
    #[Groups(['switches.read','switches.write','ipaddress.read'])]
    #[ORM\Column(length: 255)]
    private ?string $modelo = null;

    #[ORM\Column,]
    #[Groups(['switches.read','switches.write','ipaddress.read'])]
    private ?bool $estadoConexion = null;

    #[ORM\ManyToOne]
    #[Groups(['switches.read','switches.write','ipaddress.read'])]
    private ?Sector $sector = null;

    #[ORM\ManyToOne]
    #[Groups(['switches.read','switches.write','ipaddress.read'])]
    private ?Agente $agente = null;
    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['switches.read','switches.write','ipaddress.read'])]
    private ?string $etiqueta = null;

    #[Groups(['switches.read','switches.write'])]
    #[ORM\OneToOne(mappedBy: 'switches', cascade: ['persist', 'remove'])]
    private ?IpAdress $ipadress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getModelo(): ?string
    {
        return $this->modelo;
    }

    public function setModelo(string $modelo): self
    {
        $this->modelo = $modelo;

        return $this;
    }

    public function isEstadoConexion(): ?bool
    {
        return $this->estadoConexion;
    }

    public function setEstadoConexion(bool $estadoConexion): self
    {
        $this->estadoConexion = $estadoConexion;

        return $this;
    }


    public function getSector(): ?Sector
    {
        return $this->sector;
    }

    public function setSector(?Sector $sector): self
    {
        $this->sector = $sector;

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

    public function getEtiqueta(): ?string
    {
        return $this->etiqueta;
    }

    public function setEtiqueta(?string $etiqueta): self
    {
        $this->etiqueta = $etiqueta;

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
            $this->ipadress->setSwitches(null);
        }

        // set the owning side of the relation if necessary
        if ($ipadress !== null && $ipadress->getSwitches() !== $this) {
            $ipadress->getSwitches($this);
        }

        $this->ipadress = $ipadress;

        return $this;
    }

}
