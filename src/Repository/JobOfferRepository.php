<?php

namespace App\Repository;

use App\Entity\JobOffer;
use App\Entity\JobOfferSearch;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<JobOffer>
 *
 * @method JobOffer|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobOffer|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobOffer[]    findAll()
 * @method JobOffer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobOffer::class);
    }

    public function save(JobOffer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JobOffer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findLikeName(JobOfferSearch $jobOfferSearch): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        if (!empty($jobOfferSearch->getSearch())) {
            $queryBuilder->where('j.job LIKE :name')
                ->setParameter('name', '%' . $jobOfferSearch->getSearch() . '%');
        }


        if (!empty($jobOfferSearch->getContract())) {
            $queryBuilder->andWhere('j.contract LIKE :contract')
                ->setParameter('contract', $jobOfferSearch->getContract());
        }

        $queryBuilder
            ->orderBy('j.job', 'ASC');
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    //    /**
    //     * @return JobOffer[] Returns an array of JobOffer objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('j.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?JobOffer
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
