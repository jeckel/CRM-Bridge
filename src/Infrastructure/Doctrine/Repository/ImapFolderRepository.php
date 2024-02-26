<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\ImapFolder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<ImapFolder>
 *
 * @method ImapFolder|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImapFolder|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImapFolder[]    findAll()
 * @method ImapFolder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImapFolderRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImapFolder::class);
    }
}
