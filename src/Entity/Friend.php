<?php

namespace App\Entity;

use App\Repository\FriendRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FriendRepository::class)]
class Friend
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:friend', 'user:profile'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'friends')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:friend'])]
    private ?User $friend = null;

    #[ORM\Column]
    #[Groups(['user:friend', 'user:profile'])]
    private ?\DateTimeImmutable $since = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:friend', 'user:profile'])]
    private ?Group $conversation = null;

    #[Groups(['user:friend', 'user:profile'])]
    private array $mutual = [];

    public function __construct()
    {
        $this->since = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function hasUser(User $user): bool
    {
        return $this->user === $user || $this->friend === $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getFriend(): ?User
    {
        return $this->friend;
    }

    public function setFriend(?User $friend): static
    {
        $this->friend = $friend;

        return $this;
    }

    public function getSince(): ?\DateTimeImmutable
    {
        return $this->since;
    }

    public function setSince(\DateTimeImmutable $since): static
    {
        $this->since = $since;

        return $this;
    }

    public function getConversation(): ?Group
    {
        return $this->conversation;
    }

    public function setConversation(?Group $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getMutual(): ?array
    {
        return $this->mutual;
    }

    public function setMutual(array $mutual): static
    {
        $this->mutual = $mutual;
        return $this;
    }
}
