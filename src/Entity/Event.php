<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Assert\Expression("this.getEndAt() >= this.getStartAt()")
     */
    private $endAt;

    /**
     * @ORM\Column(type="time")
     * @Assert\NotBlank
     */
    private $timePrecision;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="event", orphanRemoval=true)
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Availability", mappedBy="event", orphanRemoval=true)
     */
    private $availabilities;

    /**
     * @ORM\Column(type="boolean")
     */
    private $allowSubmissions;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->availabilities = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getTimePrecision(): ?\DateTimeInterface
    {
        return $this->timePrecision;
    }

    public function setTimePrecision(\DateTimeInterface $timePrecision): self
    {
        $this->timePrecision = $timePrecision;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setEvent($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getEvent() === $this) {
                $task->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Availability[]
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availability $availability): self
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities[] = $availability;
            $availability->setEvent($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): self
    {
        if ($this->availabilities->contains($availability)) {
            $this->availabilities->removeElement($availability);
            // set the owning side to null (unless already changed)
            if ($availability->getEvent() === $this) {
                $availability->setEvent(null);
            }
        }

        return $this;
    }

    public function getAllowSubmissions(): ?bool
    {
        return $this->allowSubmissions;
    }

    public function setAllowSubmissions(bool $allowSubmissions): self
    {
        $this->allowSubmissions = $allowSubmissions;

        return $this;
    }

    /**
     * @param \DateTime|null $now : for test purpose
     * @return bool : true if event is finished, else false
     * @throws \Exception
     */
    public function isFinished(\DateTime $now = null): bool
    {
        $now = $now ?? new \DateTime();

        return $now > $this->getEndAt();
    }
}
