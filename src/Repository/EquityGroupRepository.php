<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\EquityGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EquityGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquityGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquityGroup[]    findAll()
 * @method EquityGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquityGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EquityGroup::class);
    }

    /**
     * @param Event $event
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAllForEvent(Event $event)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.event = :event')
            ->setParameter('event', $event)
        ;
    }

    // /**
    //  * @return Group[] Returns an array of Group objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Group
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
