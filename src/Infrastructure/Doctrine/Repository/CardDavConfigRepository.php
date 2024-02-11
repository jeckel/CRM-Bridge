<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\CardDavConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<CardDavConfig>
 *
 * @method CardDavConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardDavConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardDavConfig[]    findAll()
 * @method CardDavConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardDavConfigRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardDavConfig::class);
    }
}
