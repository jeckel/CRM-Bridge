<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\AccountService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<AccountService>
 *
 * @method AccountService|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountService|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountService[]    findAll()
 * @method AccountService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountServiceRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountService::class);
    }

    public function findOneByAccessToken(string $accessToken): ?AccountService
    {
        return $this->findOneBy(['accessToken' => $accessToken]);
    }
}
