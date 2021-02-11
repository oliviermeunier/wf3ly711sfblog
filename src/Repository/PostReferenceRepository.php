<?php

namespace App\Repository;

use App\Entity\PostReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostReference[]    findAll()
 * @method PostReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostReference::class);
    }

    // /**
    //  * @return PostReference[] Returns an array of PostReference objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PostReference
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
