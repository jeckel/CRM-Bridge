<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\ServiceConnector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<ServiceConnector>
 *
 * @method ServiceConnector|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceConnector|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceConnector[]    findAll()
 * @method ServiceConnector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceConnectorRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceConnector::class);
    }

    public function findOneByAccessToken(string $accessToken): ?ServiceConnector
    {
        return $this->findOneBy(['accessToken' => $accessToken]);
    }
}
