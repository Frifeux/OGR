<?php

namespace App\Entity;

use App\Repository\MeetingRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Translation\TranslatableMessage;

#[ORM\Entity(repositoryClass: MeetingRoomRepository::class)]
#[UniqueEntity(
    fields: ["name"],
    message: new TranslatableMessage("La salle {{ value }} existe déjà.")
)]
class MeetingRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $location;

    #[ORM\OneToMany(mappedBy: 'meetingRoom', targetEntity: MeetingRoomReservation::class, orphanRemoval: true)]
    private $meetingRoomReservations;

    #[ORM\Column(type: 'boolean')]
    private $enabled;

    #[ORM\Column(type: 'integer')]
    private $Capacity;

    public function __construct()
    {
        $this->meetingRoomReservations = new ArrayCollection();

        $this->enabled = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $meetingRoomReservation->setMeetingRoom($this);
        }

        return $this;
    }

    public function removeMeetingRoomReservation(MeetingRoomReservation $meetingRoomReservation): self
    {
        if ($this->meetingRoomReservations->removeElement($meetingRoomReservation)) {
            // set the owning side to null (unless already changed)
            if ($meetingRoomReservation->getMeetingRoom() === $this) {
                $meetingRoomReservation->setMeetingRoom(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
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

    public function getCapacity(): ?int
    {
        return $this->Capacity;
    }

    public function setCapacity(int $Capacity): self
    {
        $this->Capacity = $Capacity;

        return $this;
    }
}
