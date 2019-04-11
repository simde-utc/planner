<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=800, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $minWorkingTime;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $maxWorkingTime;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="tasks", cascade={"persist"})
     */
    private $groups;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->groups = new ArrayCollection();
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

    public function setLocation(?string $location): self
    {
        $this->location = $location;

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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

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

    public function getMinWorkingTime(): ?\DateTimeInterface
    {
        return $this->minWorkingTime;
    }

    public function setMinWorkingTime(?\DateTimeInterface $minWorkingTime): self
    {
        $this->minWorkingTime = $minWorkingTime;

        return $this;
    }

    public function getMaxWorkingTime(): ?\DateTimeInterface
    {
        return $this->maxWorkingTime;
    }

    public function setMaxWorkingTime(?\DateTimeInterface $maxWorkingTime): self
    {
        $this->maxWorkingTime = $maxWorkingTime;

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addTask($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeTask($this);
        }

        return $this;
    }
}
