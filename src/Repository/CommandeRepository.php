<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

//    /**
//     * @return Commande[] Returns an array of Commande objects
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

//    public function findOneBySomeField($value): ?Commande
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function searchByTerm($term)
{
    $results = $this->createQueryBuilder('c')
        ->andWhere('c.produits LIKE :term OR c.prix LIKE :term OR c.adresse LIKE :term OR c.num_tel LIKE :term')
        ->setParameter('term', '%' . $term . '%')
        ->getQuery()
        ->getResult();

    // Convert prix to string in PHP
    foreach ($results as $result) {
        $result->setPrix((string) $result->getPrix());
    }

    return $results;
}
public function orderByFieldDESC($fieldName)
{
    return $this->createQueryBuilder('c')
        ->orderBy("c.$fieldName", 'DESC')
        ->getQuery()
        ->getResult();
}
public function countByPrixGreaterThan($prix): int
{
    return $this->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->andWhere('c.prix > :prix')
        ->setParameter('prix', $prix)
        ->getQuery()
        ->getSingleScalarResult();
}

public function countByPrixLessThanOrEqual($prix): int
{
    return $this->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->andWhere('c.prix <= :prix')
        ->setParameter('prix', $prix)
        ->getQuery()
        ->getSingleScalarResult();
}

}
