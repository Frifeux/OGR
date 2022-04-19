<?php

namespace App\Entity;

use App\Repository\MeetingRoomReservationRepository;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetingRoomReservationRepository::class)]
class MeetingRoomReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

    #[ORM\Column(type: 'datetime')]
    private $startAt;

    #[ORM\Column(type: 'datetime')]
    #[Assert\GreaterThan(
        propertyPath: 'startAt',
        message: new TranslatableMessage('La date de fin doit être supérieure à la date de début')
    )]
    private $endAt;

    #[ORM\ManyToOne(targetEntity: MeetingRoom::class, inversedBy: 'meetingRoomReservations')]
    #[ORM\JoinColumn(nullable: false)]
    private $meetingRoom;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'meetingRoomReservations')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartAt(): ?\DateTime
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTime $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTime $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getMeetingRoom(): ?MeetingRoom
    {
        return $this->meetingRoom;
    }

    public function setMeetingRoom(?MeetingRoom $meetingRoom): self
    {
        $this->meetingRoom = $meetingRoom;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
