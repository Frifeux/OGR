<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\Column(type: 'string', length: 255)]
    private $location;

    #[ORM\OneToMany(mappedBy: 'equipment', targetEntity: EquipmentReservation::class, orphanRemoval: true)]
    private $equipmentReservations;

    public function __construct()
    {
        $this->equipmentReservations = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
            $equipmentReservation->setEquipment($this);
        }

        return $this;
    }

    public function removeEquipmentReservation(EquipmentReservation $equipmentReservation): self
    {
        if ($this->equipmentReservations->removeElement($equipmentReservation)) {
            // set the owning side to null (unless already changed)
            if ($equipmentReservation->getEquipment() === $this) {
                $equipmentReservation->setEquipment(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
