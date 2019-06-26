<?php

namespace App\Repository;

use App\Entity\Event;
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

    public function getUserTaskForEvent(Event $event, $withSkills = false, $withUsers)
    {
        $qb = $this->createQueryBuilder('ut')
            ->join('ut.task', 't')
            ->addSelect('t')
            ->andWhere('t.event = :event')
            ->setParameter('event', $event->getId())
        ;

        if ($withSkills) {
            $qb = $qb
                ->leftJoin('t.skills', 's')
                ->addSelect('s')
            ;
            if ($withUsers) {
                $qb = $qb
                    ->leftJoin('s.users', 'u')
                    ->addSelect('u')
                ;
            }
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
