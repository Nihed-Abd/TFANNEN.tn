<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

//    /**
//     * @return Reclamation[] Returns an array of Reclamation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reclamation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

   /**
     * Find all reclamations along with their associated responses.
     *
     * @return Reclamation[]
     */
    public function findAllWithResponsesByUserId($user_id): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.reponse', 'reponse')
            ->addSelect('reponse')
            ->andWhere('r.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getResult();
    }


      /**
     * Find reclamations by object or type.
     *
     * @param string|null $object The object to search for.
     * @param string|null $type   The type to search for.
     *
     * @return Reclamation[] An array of Reclamation objects matching the criteria.
     */
    public function findByObjectOrType(?string $objet, ?string $type): array
    {
        $qb = $this->createQueryBuilder('r');

        if ($objet !== null) {
            $qb->andWhere('r.objet = :objet')
               ->setParameter('objet', $objet);
        }

        if ($type !== null) {
            $qb->andWhere('r.type_de_reclamation = :type')
               ->setParameter('type', $type);
        }

        return $qb->getQuery()->getResult();
    }
    
}
