<?php

namespace App\Repository;

use App\Entity\TimeInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TimeInterval|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeInterval|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeInterval[]    findAll()
 * @method TimeInterval[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeIntervalRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TimeInterval::class);
    }

    // /**
    //  * @return TimeInterval[] Returns an array of TimeInterval objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimeInterval
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
