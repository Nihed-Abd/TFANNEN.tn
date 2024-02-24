<?php

namespace App\Repository;

use App\Entity\Reponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }

    /**
     * Find responses by search query (combining status and decision).
     *
     * @param string $searchQuery The search query containing status and decision
     * @return Reponse[] The array of Reponse objects matching the search criteria
     */
    public function findBySearchQuery(string $searchQuery): array
    {
        // Split the search query into status and decision values
        $keywords = explode(' ', $searchQuery);

        // Use the exploded values to construct the query
        $queryBuilder = $this->createQueryBuilder('r');

        foreach ($keywords as $index => $keyword) {
            $queryBuilder
                ->andWhere('r.status LIKE :keyword'.$index)
                ->orWhere('r.decision LIKE :keyword'.$index)
                ->setParameter('keyword'.$index, '%'.$keyword.'%');
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
