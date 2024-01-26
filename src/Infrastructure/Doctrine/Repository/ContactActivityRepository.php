<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\ContactActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactActivity>
 *
 * @method ContactActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactActivity[]    findAll()
 * @method ContactActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactActivity::class);
    }

    //    /**
    //     * @return ContactActivity[] Returns an array of ContactActivity objects
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

    //    public function findOneBySomeField($value): ?ContactActivity
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
