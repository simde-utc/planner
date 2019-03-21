<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AvailabilityRepository")
 */
class Availability
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="availabilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fullyAvailable;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TimeInterval", mappedBy="availability", orphanRemoval=true)
     */
    private $timeIntervals;

    public function __construct()
    {
        $this->timeIntervals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getFullyAvailable(): ?bool
    {
        return $this->fullyAvailable;
    }

    public function setFullyAvailable(bool $fullyAvailable): self
    {
        $this->fullyAvailable = $fullyAvailable;

        return $this;
    }

    /**
     * @return Collection|TimeInterval[]
     */
    public function getTimeIntervals(): Collection
    {
        return $this->timeIntervals;
    }

    public function addTimeInterval(TimeInterval $timeInterval): self
    {
        if (!$this->timeIntervals->contains($timeInterval)) {
            $this->timeIntervals[] = $timeInterval;
            $timeInterval->setAvailability($this);
        }

        return $this;
    }

    public function removeTimeInterval(TimeInterval $timeInterval): self
    {
        if ($this->timeIntervals->contains($timeInterval)) {
            $this->timeIntervals->removeElement($timeInterval);
            // set the owning side to null (unless already changed)
            if ($timeInterval->getAvailability() === $this) {
                $timeInterval->setAvailability(null);
            }
        }

        return $this;
    }
}
