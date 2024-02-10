<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\ImapConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImapConfig>
 *
 * @method ImapConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImapConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImapConfig[]    findAll()
 * @method ImapConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImapConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImapConfig::class);
    }
}
