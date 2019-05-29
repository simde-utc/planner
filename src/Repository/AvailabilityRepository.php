<?php

namespace App\Repository;

use App\Entity\Availability;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Availability|null find($id, $lockMode = null, $lockVersion = null)
 * @method Availability|null findOneBy(array $criteria, array $orderBy = null)
 * @method Availability[]    findAll()
 * @method Availability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvailabilityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Availability::class);
    }

    public function findAllUsersForEvent(Event $event)
    {
        return $this->createQbAllUsersForEvent($event)->getQuery()->getResult();
    }

    public function findAvailableUsersForEvent(Event $event)
    {
        $qb = $this->createQbAllUsersForEvent($event)
            ->andWhere('a.isAvailable = true')
        ;
        return $qb->getQuery()->getResult();
    }

    protected function createQbAllUsersForEvent(Event $event)
    {
        return $this
            ->createQueryBuilder('a')
            ->join('a.user', 'u')
            ->join('a.event', 'e')
            ->leftJoin('a.timeIntervals', 'ti')
            ->andWhere('e = :eventId')
            ->setParameter('eventId', $event->getId())
        ;
    }

    // /**
    //  * @return Availability[] Returns an array of Availability objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Availability
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
