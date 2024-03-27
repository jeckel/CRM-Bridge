<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 17:44
 */
declare(strict_types=1);

namespace App\Component\WebMail\Infrastructure\Doctrine\Repository;

use App\Component\WebMail\Domain\Entity\ImapMail;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<ImapMail>
 *
 * @method ImapMail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImapMail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImapMail[]    findAll()
 * @method ImapMail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImapMailRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImapMail::class);
    }
}
