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
        $this->assertEventExists();
        $eventStart = $this->getTask()->getEvent()->getStartAt();
        $relativeStart = $this->getRelativeStartAt();
        $interval = $this->transformToTimeInterval($relativeStart);

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
        $this->assertEventExists();
        $eventStart = $this->getTask()->getEvent()->getStartAt();
        $relativeEnd = $this->getRelativeEndAt();
        $interval = $this->transformToTimeInterval($relativeEnd);

        return $eventStart->add($interval);
    }

    public function setStartAt(\DateTime $startAt): self
    {
        $relativeStartAt = $this->computeRelativeDate($startAt);
        $this->setRelativeStartAt($relativeStartAt);

        return $this;
    }

    public function setEndAt(\DateTime $endAt): self
    {
        $relativeEndAt = $this->computeRelativeDate($endAt);
        $this->setRelativeEndAt($relativeEndAt);

        return $this;
    }

    private function computeRelativeDate(\DateTime $date): \DateTime
    {
        $this->assertEventExists();
        $eventStart = $this->getTask()->getEvent()->getStartAt();

        $relative = $date->diff($eventStart);

        return $this->transformToDateTime($relative);
    }

    private function transformToDateTime(\DateInterval $dateInterval): \DateTime
    {
        $date = new \DateTime(sprintf('%s:%s:%s',
            $dateInterval->h,
            $dateInterval->i,
            $dateInterval->s
        ));

        return $date;
    }

    private function transformToTimeInterval(\DateTime $dateTime): \DateInterval
    {
        $interval = new \DateInterval(sprintf('PT%sH%sM%dS',
            $dateTime->format('H'),
            $dateTime->format('i'),
            $dateTime->format('s')
        ));

        return $interval;
    }

    private function assertEventExists()
    {
        if ($this->getTask() === null) {
            throw new \InvalidArgumentException("A Requirement object should have a task associated to it.");
        }
        if ($this->getTask()->getEvent() === null) {
            throw new \InvalidArgumentException("A Requirement object should have a task with an event associated to it.");
        }
    }
}
