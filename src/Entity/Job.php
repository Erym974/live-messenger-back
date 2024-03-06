<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: JobRepository::class)]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['jobs:read'])]
    private ?int $id = null;

    #[Groups(['jobs:read'])]
    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[Groups(['jobs:read'])]
    #[ORM\Column(length: 50)]
    private ?string $category = null;

    #[Groups(['jobs:read'])]
    #[ORM\Column(length: 20)]
    private ?string $location = null;

    #[Groups(['jobs:read'])]
    #[ORM\Column(length: 150)]
    private ?string $short_description = null;

    #[Groups(['jobs:read'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $long_description = null;

    #[Groups(['jobs:read'])]
    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $requirements = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(string $short_description): static
    {
        $this->short_description = $short_description;

        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->long_description;
    }

    public function setLongDescription(string $long_description): static
    {
        $this->long_description = $long_description;

        return $this;
    }

    public function getRequirements(): ?array
    {
        return $this->requirements;
    }

    public function setRequirements(?array $requirements): static
    {
        $this->requirements = $requirements;

        return $this;
    }


}
