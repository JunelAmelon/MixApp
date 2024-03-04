<?php

namespace App\Repository;

use App\Entity\Ingenieur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ingenieur>
 *
 * @method Ingenieur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingenieur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingenieur[]    findAll()
 * @method Ingenieur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngenieurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingenieur::class);
    }

    //    /**
    //     * @return Ingenieur[] Returns an array of Ingenieur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ingenieur
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
