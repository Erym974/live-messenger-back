<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\EntityListeners(['App\EntityListener\GroupListener'])]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:groups', 'group:read', 'user:friend', 'user:profile', 'messages:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['user:groups', 'group:read'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'groups', fetch: "LAZY")]
    #[Groups(['group:read', 'user:groups'])]
    private Collection $members;

    #[ORM\OneToMany(mappedBy: 'conversation', fetch: "LAZY", targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[Groups(['user:groups', 'group:read'])]
    private ?Message $lastMessage = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user:groups', 'group:read'])]
    private ?File $picture = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Groups(['user:groups', 'group:read'])]
    private ?string $emoji = "👍";

    #[ORM\ManyToOne(fetch: "LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user:groups', 'group:read'])]
    private ?User $administrator = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastActivity = null;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:groups', 'group:read'])]
    private ?bool $private = false;

    private ?string $tempPath = null;



    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function hasMember(User $user): bool
    {
        return $this->members->contains($user);
    }

    public function setMembers(array $members): static
    {
        $this->members = new ArrayCollection($members);
        return $this;
    }

    public function addMember(User $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }
        return $this;
    }

    public function removeMember(User $member): static
    {
        $this->members->removeElement($member);
        $this->members = new ArrayCollection($this->members->getValues());
        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }

    public function getPicture(bool $entity = false): File|null|string
    {
        if($entity) return $this->picture;
        if($this->tempPath) return $this->tempPath;
        if($this->picture == null) return null;
        return $this->picture->getPath();
    }

    public function setTempPicture(string $tempPath) : static
    {
        $this->tempPath = $tempPath;
        return $this;
    }

    public function setPicture(?File $picture): static
    {
        $this->picture = $picture;
        return $this;
    }

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    public function setEmoji(?string $emoji): static
    {
        $this->emoji = $emoji;
        return $this;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?Message $lastMessage): static
    {
        $this->lastMessage = $lastMessage;
        return $this;
    }

    public function getAdministrator(): ?User
    {
        return $this->administrator;
    }

    public function setAdministrator(?User $administrator): static
    {
        $this->administrator = $administrator;

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

    public function getLastActivity(): ?\DateTimeImmutable
    {
        return $this->lastActivity;
    }

    public function setLastActivity(\DateTimeImmutable $lastActivity): static
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    public function isPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): static
    {
        $this->private = $private;

        return $this;
    }
}
