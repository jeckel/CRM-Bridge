<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\ImapMailbox;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<ImapMailbox>
 *
 * @method ImapMailbox|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImapMailbox|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImapMailbox[]    findAll()
 * @method ImapMailbox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImapFolderRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImapMailbox::class);
    }
}
