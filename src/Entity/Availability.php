<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AvailabilityRepository")
 * @ORM\Table(name="availability", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="availability_user_event", columns={"user_id", "event_id"})
 * })
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="availabilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TimeInterval", mappedBy="availability", orphanRemoval=true)
     */
    private $timeIntervals;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAvailable;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="availabilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="EquityGroup", inversedBy="availabilities")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $equityGroup;

    public function __construct()
    {
        $this->timeIntervals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setIsAvailable(?bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * L'utilisateur est disponible pendant au
     * moins une partie de l'évènement.
     * Retourne null si l'utilisateur n'a pas encore répondu
     * @return bool|null
     */
    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    /**
     * L'utilisateur n'a pas répondu à l'invitation
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->isAvailable() === null;
    }

    /**
     * L'utilisateur est disponible pendant toute la durée de l'évènement,
     * s'il n'a pas précisé de dates de disponibilité
     * (On suppose que les dates de disponibilités sont cohérentes avec
     * les dates de début et fin d'évènement)
     * @return bool
     */
    public function isFullyAvailable(): bool
    {
        return $this->isAvailable() && $this->getTimeIntervals()->count() === 0;
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

    public function getEquityGroup(): ?EquityGroup
    {
        return $this->equityGroup;
    }

    public function setEquityGroup(?EquityGroup $equityGroup): self
    {
        $this->equityGroup = $equityGroup;

        return $this;
    }
}
