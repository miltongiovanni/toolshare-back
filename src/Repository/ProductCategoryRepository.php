<?php

namespace App\Repository;

use App\Entity\ProductCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductCategory>
 */
class ProductCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductCategory::class);
    }

        public function getProductCategories($locale): array
        {

            $qb = $this->getEntityManager()->createQueryBuilder()
                ->select('p.id', 't.name', 'p.image')
                ->from(ProductCategory::class, 'p')
                ->join('p.translations', 't')
                ->andWhere('t.locale = :locale')
                ->andWhere('p.enabled = 1')
                ->setParameter('locale', $locale)
                ->orderBy('t.name', 'ASC');
            return $qb
                ->getQuery()
                ->getResult();

        }

    //    public function findOneBySomeField($value): ?ProductCategory
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
