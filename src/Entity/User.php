<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\EntityListeners(['App\EntityListener\UserListener'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'user:public', 'user:friend', 'user:groups', 'group:read', 'messages:read', 'invitation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank()]
    #[Assert\Length(min: 6, max: 100)]
    private $plainPassword;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['user:read'])]
    private $isVerified = false;

    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'user:public', 'user:friend', 'user:groups', 'group:read', 'messages:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'user:public', 'user:friend', 'user:groups', 'group:read', 'messages:read'])]
    private ?string $lastname = null;

    #[Groups(['user:read', 'user:public', 'user:friend', 'user:groups', 'group:read', 'messages:read', 'invitation:read'])]
    private ?string $fullname;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['user:read', 'user:public', 'user:friend', 'user:groups', 'group:read', 'messages:read', 'invitation:read'])]
    private ?string $profilePicture = "/default_profile_picture.png";

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['user:read', 'user:public', 'user:friend'])]
    private ?string $coverPicture = "/default_cover_picture.jpg";

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user:read', 'user:public', 'user:friend'])]
    private ?string $biography = null;

    #[ORM\Column(length: 20)]
    #[Groups(['user:read', 'user:public', 'friend:invite', 'user:friend', 'invitation:read'])]
    private ?string $friendCode = null;

    #[ORM\Column]
    #[Groups(['read:user'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'members', fetch: "LAZY")]
    private Collection $groups;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Setting::class, orphanRemoval: true)]
    #[Groups(['user:read'])]
    private Collection $settings;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Friend::class, orphanRemoval: true, fetch: "LAZY")]
    private Collection $friends;

    #[ORM\OneToMany(mappedBy: 'emitter', targetEntity: Invitation::class, fetch: "LAZY", orphanRemoval: true)]
    private Collection $invitationsSended;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Invitation::class, fetch: "LAZY", orphanRemoval: true)]
    private Collection $invitationsReceived;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->groups = new ArrayCollection();
        $this->settings = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->invitationsSended = new ArrayCollection();
        $this->invitationsReceived = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Get beautiful Role
     */
    public function getRole(): string
    {
        $roles = $this->roles;
        foreach ($roles as $role) {
            if(str_contains(strtolower($role),'admin')) return"Administrator";
            if(str_contains(strtolower($role),'moderator')) return"Moderator";
        }
        return "User";
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(string $role = "ROLE_USER"): bool
    {
        // if($role == "ROLE_USER") return true;
        return in_array($role, $this->roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function setFullname(): static
    {
       $this->fullname = $this->firstname . " " . $this->lastname;
       return $this;
    }

    public function getFullname(): string
    {
        return $this->firstname . " " . $this->lastname;
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

    public function getProfilePicture(): ?string
    {
        return "http://localhost:8000/uploads/users" . $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    public function getCoverPicture(): ?string
    {
        return "http://localhost:8000/uploads/users" . $this->coverPicture;
    }

    public function setCoverPicture(string $coverPicture): static
    {
        $this->coverPicture = $coverPicture;
        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): static
    {
        $this->biography = $biography;
        return $this;
    }

    public function getFriendCode(): ?string
    {
        return $this->friendCode;
    }

    public function setFriendCode(?string $friendCode): static
    {
        $this->friendCode = $friendCode;
        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): static
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addMember($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): static
    {
        if ($this->groups->removeElement($group)) {
            $group->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Setting>
     */
    public function getSetting(string $key): ?Setting
    {
        foreach($this->settings as $setting) {
            if($setting->getMeta()->getName() == $key) return $setting;
        }
        return null;
    }

    /**
     * @return Collection<int, Setting>
     */
    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function setSettings(array $settings): static
    {
        $this->settings = new ArrayCollection($settings);
        foreach ($settings as $setting) {
            // if setting is of Setting::class
            if(!$setting instanceof Setting) continue;
            
            $setting->setUser($this);
        }

        return $this;
    }

    public function addSetting(Setting $setting): static
    {
        if (!$this->settings->contains($setting)) {
            $this->settings->add($setting);
            $setting->setUser($this);
        }
        return $this;
    }

    public function removeSetting(Setting $setting): static
    {
        if ($this->settings->removeElement($setting)) {
            // set the owning side to null (unless already changed)
            if ($setting->getMessage() === $this) {
                $setting->setMessage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friend>
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function getFriend(User $user): ?Friend
    {
        foreach($this->friends as $friend) {
            if($friend->getFriend() === $user) return $friend;
        }
        return null;
    }

    public function hasFriend(User $user): bool
    {
        foreach($this->friends as $friend) {
            if($friend->getFriend() === $user) return true;
        }
        return false;
    }

    public function addFriend(Friend $friend): static
    {
        if (!$this->friends->contains($friend)) {
            $this->friends->add($friend);
            $friend->setUser($this);
        }

        return $this;
    }

    public function removeFriend(Friend $friend): static
    {
        if ($this->friends->removeElement($friend)) {
            // set the owning side to null (unless already changed)
            if ($friend->getUser() === $this) {
                $friend->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitationsSended(): Collection
    {
        return $this->invitationsSended;
    }

    public function addInvitationsSended(Invitation $invitationsSended): static
    {
        if (!$this->invitationsSended->contains($invitationsSended)) {
            $this->invitationsSended->add($invitationsSended);
            $invitationsSended->setEmitter($this);
        }

        return $this;
    }

    public function removeInvitationsSended(Invitation $invitationsSended): static
    {
        if ($this->invitationsSended->removeElement($invitationsSended)) {
            // set the owning side to null (unless already changed)
            if ($invitationsSended->getEmitter() === $this) {
                $invitationsSended->setEmitter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitationsReceived(): Collection
    {
        return $this->invitationsReceived;
    }

    public function addInvitationsReceived(Invitation $invitationsReceived): static
    {
        if (!$this->invitationsReceived->contains($invitationsReceived)) {
            $this->invitationsReceived->add($invitationsReceived);
            $invitationsReceived->setReceiver($this);
        }

        return $this;
    }

    public function removeInvitationsReceived(Invitation $invitationsReceived): static
    {
        if ($this->invitationsReceived->removeElement($invitationsReceived)) {
            // set the owning side to null (unless already changed)
            if ($invitationsReceived->getReceiver() === $this) {
                $invitationsReceived->setReceiver(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getFullname();
    }

}
