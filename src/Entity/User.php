<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @UniqueEntity(fields={"email"}, message="Il existe déjâ un compte avec cet email !")
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    private $plainPassword;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastname;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $location;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MeetingRoomReservation::class, orphanRemoval: true)]
    private $meetingRoomReservations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EquipmentReservation::class, orphanRemoval: true)]
    private $equipmentReservations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: OfficeReservation::class, orphanRemoval: true)]
    private $officeReservations;

    #[ORM\Column(type: 'boolean')]
    private $enabled;

    public function __construct()
    {
        $this->meetingRoomReservations = new ArrayCollection();
        $this->equipmentReservations = new ArrayCollection();
        $this->officeReservations = new ArrayCollection();

        $this->enabled = true;
        $this->createdAt = new \DateTimeImmutable("now");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = strtoupper($lastname);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|MeetingRoomReservation[]
     */
    public function getMeetingRoomReservations(): Collection
    {
        return $this->meetingRoomReservations;
    }

    public function addMeetingRoomReservation(MeetingRoomReservation $meetingRoomReservation): self
    {
        if (!$this->meetingRoomReservations->contains($meetingRoomReservation)) {
            $this->meetingRoomReservations[] = $meetingRoomReservation;
            $meetingRoomReservation->setUser($this);
        }

        return $this;
    }

    public function removeMeetingRoomReservation(MeetingRoomReservation $meetingRoomReservation): self
    {
        if ($this->meetingRoomReservations->removeElement($meetingRoomReservation)) {
            // set the owning side to null (unless already changed)
            if ($meetingRoomReservation->getUser() === $this) {
                $meetingRoomReservation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EquipmentReservation[]
     */
    public function getEquipmentReservations(): Collection
    {
        return $this->equipmentReservations;
    }

    public function addEquipmentReservation(EquipmentReservation $equipmentReservation): self
    {
        if (!$this->equipmentReservations->contains($equipmentReservation)) {
            $this->equipmentReservations[] = $equipmentReservation;
            $equipmentReservation->setUser($this);
        }

        return $this;
    }

    public function removeEquipmentReservation(EquipmentReservation $equipmentReservation): self
    {
        if ($this->equipmentReservations->removeElement($equipmentReservation)) {
            // set the owning side to null (unless already changed)
            if ($equipmentReservation->getUser() === $this) {
                $equipmentReservation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OfficeReservation[]
     */
    public function getOfficeReservations(): Collection
    {
        return $this->officeReservations;
    }

    public function addOfficeReservation(OfficeReservation $officeReservation): self
    {
        if (!$this->officeReservations->contains($officeReservation)) {
            $this->officeReservations[] = $officeReservation;
            $officeReservation->setUser($this);
        }

        return $this;
    }

    public function removeOfficeReservation(OfficeReservation $officeReservation): self
    {
        if ($this->officeReservations->removeElement($officeReservation)) {
            // set the owning side to null (unless already changed)
            if ($officeReservation->getUser() === $this) {
                $officeReservation->setUser(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
