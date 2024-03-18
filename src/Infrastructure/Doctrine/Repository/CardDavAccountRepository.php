<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Component\CardDav\Domain\Entity\CardDavAccount;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<CardDavAccount>
 *
 * @method CardDavAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardDavAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardDavAccount[]    findAll()
 * @method CardDavAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardDavAccountRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardDavAccount::class);
    }
}
