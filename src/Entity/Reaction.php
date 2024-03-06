<?php

namespace App\Entity;

use App\Repository\ReactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReactionRepository::class)]
class Reaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Groups(['messages:read'])]
    private ?string $content = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[Groups(['messages:read'])]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'reaction')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Message $message = null;

    #[Groups(['messages:read'])]
    private int $count = 0;

    // #[Groups(['messages:read'])]
    private bool $reacted = false;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function setCount(): static
    {
        $this->count = count($this->getUsers());
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setReacted(bool $reacted): static
    {
        $this->reacted = $reacted;
        return $this;
    }

    public function getReacted(): int
    {
        return $this->reacted;
    }

}
