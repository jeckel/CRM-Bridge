<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Setup\Query;

use App\Component\Shared\Identity\CardDavAccountId;
use App\Component\Shared\Identity\CardDavAddressBookId;
use Doctrine\ORM\EntityManagerInterface;

readonly class ListCardDavAddressBooks
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    /**
     * @param CardDavAccountId $accountId
     * @return array<string, string>
     */
    public function __invoke(CardDavAccountId $accountId): array
    {
        /** @var array{name: string, id: CardDavAddressBookId}[] $addressBooks */
        $addressBooks = $this->entityManager->createQuery(
            'SELECT c.name, c.id
            FROM App\Component\CardDav\Domain\Entity\CardDavAddressBook c
            WHERE c.account = :accountId'
        )
            ->setParameter('accountId', $accountId)
            ->getResult();
        return array_combine(
            array_map(static fn($addressBook): string => $addressBook['name'], $addressBooks),
            array_map(static fn($addressBook): string => (string) $addressBook['id'], $addressBooks),
        );
    }
}
