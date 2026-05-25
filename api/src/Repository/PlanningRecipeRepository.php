<?php

namespace App\Repository;

use App\Entity\PlanningRecipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanningRecipe>
 */
class PlanningRecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanningRecipe::class);
    }

    public function findByPlanning(int $planningId): array
    {
        return $this->createQueryBuilder('pr')
            ->join('pr.recipe', 'r')
            ->addSelect('r')
            ->where('pr.planning = :planningId')
            ->setParameter('planningId', $planningId)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return PlanningRecipe[] Returns an array of PlanningRecipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PlanningRecipe
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
