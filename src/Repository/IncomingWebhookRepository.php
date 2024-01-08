<?php

namespace App\Repository;

use App\Entity\IncomingWebhook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IncomingWebhook>
 *
 * @method IncomingWebhook|null find($id, $lockMode = null, $lockVersion = null)
 * @method IncomingWebhook|null findOneBy(array $criteria, array $orderBy = null)
 * @method IncomingWebhook[]    findAll()
 * @method IncomingWebhook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncomingWebhookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IncomingWebhook::class);
    }

    public function persist(IncomingWebhook $incomingWebhook): void
    {
        $this->getEntityManager()->persist($incomingWebhook);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return IncomingWebhook[] Returns an array of IncomingWebhook objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?IncomingWebhook
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
