<?php

namespace App\Repository;

use App\Entity\AudiosProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AudiosProjet>
 *
 * @method AudiosProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method AudiosProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method AudiosProjet[]    findAll()
 * @method AudiosProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AudiosProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AudiosProjet::class);
    }

    //    /**
    //     * @return AudiosProjet[] Returns an array of AudiosProjet objects
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

    //    public function findOneBySomeField($value): ?AudiosProjet
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
