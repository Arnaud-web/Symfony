<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
    * @return Article[] Returns an array of Article objects
    */
    
    public function findByUserId($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'DESC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByCategorieId($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.category = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'DESC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAll()
    {
        return $this->createQueryBuilder('a')
            // ->andWhere('a.user = :val')
            // ->setParameter('val', $value)
            ->orderBy('a.id', 'DESC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    // public function findNext($value)
    // {
    //     return $this->createQueryBuilder('a')
    //         ->andWhere('a.id < :val')
    //         ->setParameter('val', $value)
    //         // ->orderBy('a.id', 'DESC')
    //         ->setMaxResults(3)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }



    

    /*
    public function findOneBySomeField($value): ?Article
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
