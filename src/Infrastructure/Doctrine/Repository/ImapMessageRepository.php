<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\ImapMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImapMessage>
 *
 * @method ImapMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImapMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImapMessage[]    findAll()
 * @method ImapMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImapMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImapMessage::class);
    }

    public function persist(ImapMessage $mail): void
    {
        $this->getEntityManager()->persist($mail);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return Mail[] Returns an array of Mail objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Mail
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getById(string $mailId): ImapMessage
    {
        $mail = $this->find($mailId);
        if (null === $mail) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(ImapMessage::class, [$mailId]);
        }
        return $mail;
    }
}
