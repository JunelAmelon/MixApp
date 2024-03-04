<?php

namespace App\Repository;

use App\Entity\Audios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Audios>
 *
 * @method Audios|null find($id, $lockMode = null, $lockVersion = null)
 * @method Audios|null findOneBy(array $criteria, array $orderBy = null)
 * @method Audios[]    findAll()
 * @method Audios[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AudiosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Audios::class);
    }

    //    /**
    //     * @return Audios[] Returns an array of Audios objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Audios
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
