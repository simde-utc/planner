<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RequirementRepository")
 */
class Requirement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $requirements;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Task", inversedBy="requirements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $task;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    private $relativeStartAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    private $relativeEndAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequirements(): ?int
    {
        return $this->requirements;
    }

    public function setRequirements(int $requirements): self
    {
        $this->requirements = $requirements;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getRelativeStartAt(): ?\DateTime
    {
        return $this->relativeStartAt;
    }

    public function setRelativeStartAt(\DateTime $relativeStartAt): self
    {
        $this->relativeStartAt = $relativeStartAt;

        return $this;
    }

    public function getRelativeEndAt(): ?\DateTime
    {
        return $this->relativeEndAt;
    }

    public function setRelativeEndAt(\DateTime $relativeEndAt): self
    {
        $this->relativeEndAt = $relativeEndAt;

        return $this;
    }

    /**
     * Return the absolute start datetime
     *
     * @return \DateTime|null
     * @throws \Exception
     */
    public function getStartAt(): ?\DateTime
    {
        $eventStart = $this->getTask()->getEvent()->getStartAt();
        $relativeStart = $this->getRelativeStartAt();
        $interval = new \DateInterval('PT'.$relativeStart->format('H').'H'.$relativeStart->format('i').'M');

        return $eventStart->add($interval);
    }

    /**
     * Return the absolute end datetime
     *
     * @return \DateTime|null
     * @throws \Exception
     */
    public function getEndAt(): ?\DateTime
    {
        $eventEnd = $this->getTask()->getEvent()->getEndAt();
        $relativeEnd = $this->getRelativeEndAt();
        $interval = new \DateInterval('PT'.$relativeEnd->format('H').'H'.$relativeEnd->format('i').'M');

        return $eventEnd->add($interval);
    }
}
