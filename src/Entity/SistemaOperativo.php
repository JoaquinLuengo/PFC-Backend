<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SistemaOperativoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SistemaOperativoRepository::class)]
#[ApiResource(
    
    normalizationContext: ['groups' => ['sistemaop.read']],
    denormalizationContext: ['groups' => ['sistemaop.write']]
)]
class SistemaOperativo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['sistemaop.read','sistemaop.write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['sistemaop.read','sistemaop.write'])]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    #[Groups(['sistemaop.read','sistemaop.write'])]
    private ?string $version = null;

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

    public function getversion(): ?string
    {
        return $this->version;
    }

    public function setversion(string $version): self
    {
        $this->version = $version;

        return $this;
    }
}
