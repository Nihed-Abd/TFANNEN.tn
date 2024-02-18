<?php

namespace App\Repository;

use App\Entity\Carriere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Carriere>
 *
 * @method Carriere|null find($id, $lockMode = null, $lockVersion = null)
 * @method Carriere|null findOneBy(array $criteria, array $orderBy = null)
 * @method Carriere[]    findAll()
 * @method Carriere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarriereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carriere::class);
    }

//    /**
//     * @return Carriere[] Returns an array of Carriere objects
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

//    public function findOneBySomeField($value): ?Carriere
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
