<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invitation:read', 'user:friend'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'invitationsSended')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['invitation:read', 'user:friend'])]
    private ?User $emitter = null;

    #[ORM\ManyToOne(inversedBy: 'invitationsReceived')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['invitation:read'])]
    private ?User $receiver = null;

    #[ORM\Column]
    #[Groups(['invitation:read', 'user:friend'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmitter(): ?User
    {
        return $this->emitter;
    }

    public function setEmitter(?User $emitter): static
    {
        $this->emitter = $emitter;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
