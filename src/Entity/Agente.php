<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AgenteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AgenteRepository::class)]
#[ApiResource(
    
    normalizationContext: ['groups' => ['agente.read']],
    denormalizationContext: ['groups' => ['agente.write']]
)]

class Agente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['agente.read','agente.write','switches.read','ipaddress.read'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Groups(['agente.read','agente.write','switches.read','ipaddress.read'])]
    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[Assert\NotBlank]
    #[Groups(['agente.read','agente.write','switches.read','ipaddress.read'])]
    #[ORM\Column(length: 255)]
    private ?string $apellido = null;

    #[ORM\ManyToOne]
    #[Groups(['agente.read','agente.write'])]
    private ?Sector $sector = null;

    #[Groups(['agente.read','agente.write'])]
    #[ORM\OneToOne(mappedBy: 'agente', cascade: ['persist', 'remove'])]
    private ?IpAdress $ipadress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;

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

    public function getIpadress(): ?IpAdress
    {
        return $this->ipadress;
    }

    public function setIpadress(?IpAdress $ipadress): self
    {
        // unset the owning side of the relation if necessary
        if ($ipadress === null && $this->ipadress !== null) {
            $this->ipadress->setAgente(null);
        }

        // set the owning side of the relation if necessary
        if ($ipadress !== null && $ipadress->getAgente() !== $this) {
            $ipadress->setAgente($this);
        }

        $this->ipadress = $ipadress;

        return $this;
    }

}
