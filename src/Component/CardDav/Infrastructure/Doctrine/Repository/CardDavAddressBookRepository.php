<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Infrastructure\Doctrine\Repository;

use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<CardDavAddressBook>
 *
 * @method CardDavAddressBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardDavAddressBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardDavAddressBook[]    findAll()
 * @method CardDavAddressBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardDavAddressBookRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardDavAddressBook::class);
    }
}
