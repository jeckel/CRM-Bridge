<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Query;

use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @phpstan-type ListContactItem array{
 *     id: ContactId,
 *     displayName: string,
 *     companyName: string,
 *     emailAddress: Email,
 *     addressBook: string,
 * }
 */
readonly class ContactListQuery
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ) {}

    /**
     * @param int $page
     * @param int $limit
     * @return PaginationInterface<string, ListContactItem>
     */
    public function __invoke(int $page, int $limit): PaginationInterface
    {
        $query = $this->entityManager->createQuery(
            'SELECT c.id, c.displayName, co.name as companyName, e.address as emailAddress, a.name as addressBook
            FROM \App\Component\Contact\Domain\Entity\Contact c
            INNER JOIN \App\Component\CardDav\Domain\Entity\CardDavAddressBook a WITH c.addressBook = a.id
            LEFT JOIN \App\Component\Contact\Domain\Entity\ContactEmail e WITH c.id = e.contact AND e.isPreferred = true
            LEFT JOIN \App\Component\Contact\Domain\Entity\Company co WITH c.company = co.id'
        );
        return $this->paginator->paginate(
            $query,
            $page,
            $limit,
            [
                'defaultSortFieldName' => ['c.displayName'],
                'defaultSortDirection' => 'asc',
            ]
        );
    }
}
