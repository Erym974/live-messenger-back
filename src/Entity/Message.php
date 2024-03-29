<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use App\Service\MessageStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\EntityListeners(['App\EntityListener\MessageListener'])]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['messages:read', 'user:groups'])]
    private ?int $id = null;

    #[ORM\ManyToOne(fetch: "LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['messages:read'])]
    private ?User $sender = null;

    #[ORM\Column]
    #[Groups(['messages:read'])]
    private ?\DateTimeImmutable $sended_at = null;

    // #[ORM\Column(type: Types::TEXT)]
    #[Groups(['messages:read', 'user:groups'])]
    private ?string $content = null;
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $encrypted_content = null;

    #[ORM\Column(length: 10)]
    #[Groups(['messages:read'])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['messages:read'])]
    private ?Group $conversation = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['messages:read'])]
    private ?bool $edited = false;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: Reaction::class, orphanRemoval: true)]
    #[Groups(['messages:read'])]
    private Collection $reactions;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: File::class)]
    #[Groups(['messages:read', 'user:groups'])]
    private Collection $files;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'messages')]
    #[Groups(['messages:read'])]
    private ?self $reply = null;

    public function __construct()
    {
        $this->sended_at = new \DateTimeImmutable();
        $this->reactions = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getSendedAt(): ?\DateTimeImmutable
    {
        return $this->sended_at;
    }

    public function setSendedAt(\DateTimeImmutable $sended_at): static
    {
        $this->sended_at = $sended_at;

        return $this;
    }

    public function getContent(): ?string
    {
        if($this->getStatus() === MessageStatus::DELETED) return "This message has been deleted";
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getEncryptedContent(): ?string
    {
        return $this->encrypted_content;
    }

    public function setEncryptedContent(string $encrypted_content): static
    {
        $this->encrypted_content = $encrypted_content;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getConversation(): ?Group
    {
        return $this->getGroup();
    }

    public function getGroup(): ?Group
    {
        return $this->conversation;
    }

    public function setGroup(?Group $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function isEdited(): ?bool
    {
        return $this->edited;
    }

    public function setEdited(?bool $edited): static
    {
        $this->edited = $edited;

        return $this;
    }

    /**
     * @return Collection<int, Reaction>
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function setReactions(array $reactions): static
    {
        $this->reactions = new ArrayCollection($reactions);
        foreach ($reactions as $reaction) {
            $reaction->setMessage($this);
        }

        return $this;
    }

    public function addReaction(Reaction $reaction): static
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions->add($reaction);
            $reaction->setMessage($this);
        }
        return $this;
    }

    public function removeReaction(Reaction $reaction): static
    {
        if ($this->reactions->removeElement($reaction)) {
            // set the owning side to null (unless already changed)
            if ($reaction->getMessage() === $this) {
                $reaction->setMessage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        if($this->getStatus() === MessageStatus::DELETED) return new ArrayCollection();
        return $this->files;
    }

    public function addFile(File $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setMessage($this);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getMessage() === $this) {
                $file->setMessage(null);
            }
        }

        return $this;
    }

    public function getReply(): ?self
    {
        if($this->reply && $this->reply->getReply()) $this->reply->setReply(null);
        return $this->reply;
    }

    public function setReply(?self $reply): static
    {
        $this->reply = $reply;

        return $this;
    }

}
