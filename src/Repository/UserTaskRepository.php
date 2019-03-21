<?php

namespace App\Repository;

use App\Entity\UserTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTask[]    findAll()
 * @method UserTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTaskRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserTask::class);
    }

    // /**
    //  * @return UserTask[] Returns an array of UserTask objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserTask
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
