<?php

namespace App\Repository;

use App\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Profile>
 */
class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    //    /**
    //     * @return Profil[] Returns an array of Profil objects
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

        public function getAllProfiles($locale): array
        {
            $qb = $this->getEntityManager()->createQueryBuilder()
                ->select('p.id', 'p.code', 't.slug', 't.description', 'p.created_at', 'p.updated_at')
                ->from(Profile::class, 'p')
                ->join('p.translations', 't')
                ->andWhere('t.locale = :locale')
                ->andWhere('p.enabled = 1')
                ->setParameter('locale', $locale)
                ->orderBy('t.description', 'ASC');
            return $qb
                ->getQuery()
                ->getResult();
        }
}
