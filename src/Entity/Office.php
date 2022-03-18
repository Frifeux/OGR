<?php

namespace App\Entity;

use App\Repository\OfficeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfficeRepository::class)]
class Office
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $location;

    #[ORM\Column(type: 'string', length: 255)]
    private $floor;

    #[ORM\Column(type: 'string', length: 255)]
    private $department;

    #[ORM\OneToMany(mappedBy: 'office', targetEntity: OfficeReservation::class, orphanRemoval: true)]
    private $officeReservations;

    #[ORM\Column(type: 'boolean')]
    private $enabled;

    public function __construct()
    {
        $this->officeReservations = new ArrayCollection();

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

    public function getFloor(): ?string
    {
        return $this->floor;
    }

    public function setFloor(string $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): self
    {
        $this->department = $department;

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
            $officeReservation->setOffice($this);
        }

        return $this;
    }

    public function removeOfficeReservation(OfficeReservation $officeReservation): self
    {
        if ($this->officeReservations->removeElement($officeReservation)) {
            // set the owning side to null (unless already changed)
            if ($officeReservation->getOffice() === $this) {
                $officeReservation->setOffice(null);
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
}
