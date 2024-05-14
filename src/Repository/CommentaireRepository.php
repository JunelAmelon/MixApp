<?php

namespace App\Repository;

use App\Entity\Commentaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaire>
 *
 * @method Commentaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaire[]    findAll()
 * @method Commentaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

    //    /**
    //     * @return Commentaire[] Returns an array of Commentaire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Commentaire
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findCommentairesWithUserIdQuery($id_projet, $page = 1, $limit = 10)
{
    $query = $this->createQueryBuilder('c')
        ->select('c.id, c.message, c.date, c.id_user')
        ->where('c.id_projet = :id_projet')
        ->setParameter('id_projet', $id_projet)
        ->orderBy('c.date', 'DESC')
        ->getQuery();

    // Calcule l'offset en fonction de la page et de la limite
    $offset = ($page - 1) * $limit;

    // Applique l'offset et la limite à la requête
    $query->setFirstResult($offset)
        ->setMaxResults($limit);

    return $query;
}

public function findaCommentairesWithUserIdQuery($id_projet)
{
    return $this->createQueryBuilder('c')
        ->select('c.id, c.message, c.date, c.id_user')
        ->where('c.id_projet = :id_projet')
        ->setParameter('id_projet', $id_projet)
        ->orderBy('c.date', 'DESC')
        ->getQuery()
        ->getResult();
}


public function paginate($page, $perPage)
{
    $firstResult = ($page - 1) * $perPage;

    return $this->createQueryBuilder('c')
        ->orderBy('c.date', 'DESC')
        ->setFirstResult($firstResult)
        ->setMaxResults($perPage)
        ->getQuery()
        ->getResult();
}



 
}
