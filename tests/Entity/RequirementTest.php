<?php
/**
 * Created by
 * corentinhembise
 * 2019-04-22
 */

namespace App\Tests\Entity;


use App\Entity\Event;
use App\Entity\Requirement;
use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class RequirementTest extends TestCase
{
    protected function createRequirement()
    {
        $event = new Event();
        $event->setStartAt(new \DateTime("1996-12-12 01:01:01"));
        $task = new Task();
        $task->setEvent($event);
        $requirement = new Requirement();
        $requirement->setTask($task);

        return $requirement;
    }

    public function testGetStartAt()
    {
        $requirement = $this->createRequirement();
        $requirement->setRelativeStartAt(new \DateTime("02:10:01"));

        $actualStartAt = $requirement->getStartAt();
        $expectedStartAt = new \DateTime("1996-12-12 03:11:02");

        $this->assertEquals($expectedStartAt, $actualStartAt);
    }

    public function testGetEndAt()
    {
        $requirement = $this->createRequirement();
        $requirement->setRelativeEndAt(new \DateTime("05:07:03"));

        $actualEndAt = $requirement->getEndAt();
        $expectedEndAt = new \DateTime("1996-12-12 06:08:04");

        $this->assertEquals($expectedEndAt, $actualEndAt);
    }

    public function testSetStartAt()
    {
        $requirement = $this->createRequirement();
        $requirement->setStartAt(new \DateTime("1996-12-12 07:15:31"));

        $actualRelativeStart = $requirement->getRelativeStartAt();
        $expectedRelativeStart = new \DateTime("06:14:30");

        $this->assertEquals($expectedRelativeStart, $actualRelativeStart);
    }

    public function testSetEndAt()
    {
        $requirement = $this->createRequirement();
        $requirement->setEndAt(new \DateTime("1996-12-12 07:15:31"));

        $actualRelativeEnd = $requirement->getRelativeEndAt();
        $expectedRelativeEnd = new \DateTime("06:14:30");

        $this->assertEquals($expectedRelativeEnd, $actualRelativeEnd);
    }
}