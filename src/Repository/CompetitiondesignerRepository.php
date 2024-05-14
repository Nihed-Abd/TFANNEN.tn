<?php

namespace App\Repository;

use App\Entity\Competitiondesigner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Competitiondesigner>
 *
 * @method Competitiondesigner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Competitiondesigner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Competitiondesigner[]    findAll()
 * @method Competitiondesigner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompetitiondesignerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competitiondesigner::class);
    }

//    /**
//     * @return Competitiondesigner[] Returns an array of Competitiondesigner objects
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

//    public function findOneBySomeField($value): ?Competitiondesigner
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
