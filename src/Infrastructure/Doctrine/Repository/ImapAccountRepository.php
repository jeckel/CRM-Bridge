<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\ImapAccount;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<ImapAccount>
 *
 * @method ImapAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImapAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImapAccount[]    findAll()
 * @method ImapAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImapAccountRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImapAccount::class);
    }
}
