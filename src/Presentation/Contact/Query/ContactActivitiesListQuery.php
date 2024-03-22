<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 22/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Query;

use App\Component\Shared\Identity\ContactId;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @phpstan-type ListContactActivityItem array{
 *     date: DateTimeImmutable,
 *     subject: string,
 *     description: string,
 * }
 */
class ContactActivitiesListQuery
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ) {}

    /**
     * @return PaginationInterface<string, ListContactActivityItem>
     */
    public function __invoke(ContactId $contactId, int $page, int $limit): PaginationInterface
    {
        $query = $this->entityManager->createQuery(
            'SELECT ca.date, ca.subject, ca.description
            FROM \App\Component\Contact\Domain\Entity\ContactActivity as ca
            WHERE ca.contact = :contactId'
        )->setParameter('contactId', $contactId);
        return $this->paginator->paginate(
            $query,
            $page,
            $limit,
            [
                'defaultSortFieldName' => ['ca.date'],
                'defaultSortDirection' => 'desc',
            ]
        );
    }
}
