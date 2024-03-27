<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 16:40
 */
declare(strict_types=1);

namespace App\Component\WebMail\Infrastructure\Doctrine\Repository;

use App\Component\WebMail\Domain\Entity\ImapMailbox;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<ImapMailbox>
 *
 * @method ImapMailbox|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImapMailbox|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImapMailbox[]    findAll()
 * @method ImapMailbox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImapMailboxRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImapMailbox::class);
    }
}
