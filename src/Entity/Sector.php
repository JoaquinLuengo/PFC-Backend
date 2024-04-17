<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SectorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SectorRepository::class)]
#[ApiResource(
    
    normalizationContext: ['groups' => ['sector.read']],
    denormalizationContext: ['groups' => ['sector.write']]
)]
class Sector
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['agente.read','sector.read','switches.read'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Groups(['agente.read','sector.read','sector.write','switches.read'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $nombre = null;

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
}
