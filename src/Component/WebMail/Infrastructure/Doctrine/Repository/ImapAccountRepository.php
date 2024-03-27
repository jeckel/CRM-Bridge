<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:20
 */
declare(strict_types=1);

namespace App\Component\WebMail\Infrastructure\Doctrine\Repository;

use App\Component\WebMail\Domain\Entity\ImapAccount;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
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
