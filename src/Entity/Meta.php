<?php

namespace App\Entity;

use App\Repository\MetaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MetaRepository::class)]
class Meta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $allowed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getAllowed(): ?string
    {
        return $this->allowed;
    }

    public function setAllowed(?string $allowed): static
    {
        $this->allowed = $allowed;

        return $this;
    }
}
