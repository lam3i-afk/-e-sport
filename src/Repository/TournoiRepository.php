<?php

namespace App\Repository;

use App\Entity\Tournoi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tournoi>
 */
class TournoiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tournoi::class);
    }

    public function findOneWithJeu(int $id): ?Tournoi
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.jeu', 'j')
            ->addSelect('j')
            ->leftJoin('t.participants', 'p')
            ->addSelect('p')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find tournois by optional filters: status, type and search term.
     *
     * @return Tournoi[]
     */
    public function findByFilters(?string $status, ?string $type, ?string $search): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.jeu', 'j')
            ->addSelect('j')
            ->leftJoin('t.participants', 'p')
            ->addSelect('p')
            ->orderBy('t.date_debut', 'DESC');

        if ($status) {
            $qb->andWhere('t.statut = :status')
               ->setParameter('status', $status);
        }

        if ($type) {
            $qb->andWhere('t.type = :type')
               ->setParameter('type', $type);
        }

        if ($search) {
            $qb->andWhere('t.nom LIKE :search OR j.nom LIKE :search')
               ->setParameter('search', '%'.$search.'%');
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Tournoi[] Returns an array of Tournoi objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tournoi
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
