<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param Event $event
     * @return User[]
     */
    public function getUsersForEvent(Event $event, bool $whithAvailabilitySlots = false)
    {
        return $this->queryBuilderUsersForEvent($event)->getQuery()->getResult();
    }

    /**
     * @param Event $event
     * @param bool $whithAvailabilitySlots
     * @return User[]
     */
    public function getAvailableUsersForEvent(Event $event, bool $whithAvailabilitySlots = false)
    {
        $qb = $this->queryBuilderUsersForEvent($event)->andWhere('a.isAvailable = true');

        if ($whithAvailabilitySlots) {
            $qb = $qb
                ->leftJoin('a.timeIntervals', 'ti')
                ->addSelect('ti')
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function queryBuilderUsersForEvent(Event $event)
    {
        return $this->createQueryBuilder('u')
            ->join('u.availabilities', 'a')
            ->addSelect('a')
            ->andWhere('a.event = :eventId')
            ->setParameter('eventId', $event->getId())
        ;
    }
}
