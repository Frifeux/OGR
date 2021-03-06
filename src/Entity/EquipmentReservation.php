<?php

namespace App\Entity;

use App\Repository\EquipmentReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Translation\TranslatableMessage;

#[ORM\Entity(repositoryClass: EquipmentReservationRepository::class)]
class EquipmentReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

    #[ORM\Column(type: 'datetime')]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: new TranslatableMessage('La date de début doit être supérieure ou égal à la date du jour.')
    )]
    private $startAt;

    #[ORM\Column(type: 'datetime')]
    #[Assert\GreaterThan(
        propertyPath: 'startAt',
        message: new TranslatableMessage('La date de fin doit être supérieure à la date de début')
    )]
    private $endAt;

    #[ORM\ManyToOne(targetEntity: Equipment::class, inversedBy: 'equipmentReservations')]
    #[ORM\JoinColumn(nullable: false)]
    private $equipment;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'equipmentReservations')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
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

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;

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
}
