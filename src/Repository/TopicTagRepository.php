<?php

namespace App\Repository;

use App\Entity\TopicTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TopicTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopicTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopicTag[]    findAll()
 * @method TopicTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopicTag::class);
    }

    // /**
    //  * @return TopicTag[] Returns an array of TopicTag objects
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
    public function findOneBySomeField($value): ?TopicTag
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
