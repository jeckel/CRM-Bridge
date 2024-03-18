<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\Service;

use App\Component\CardDav\Application\Port\RepositoryPort;
use App\Component\CardDav\Domain\Entity\CardDavAccount;
use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Component\CardDav\Infrastructure\CardDav\CardDavClientProvider;

readonly class AddressBookManager
{
    public function __construct(
        private CardDavClientProvider $cardDavClientProvider,
        private RepositoryPort $repository,
    ) {}

    public function fetchAddressBookFromAccount(CardDavAccount $account): void
    {
        foreach (
            $this->cardDavClientProvider->getClient(
                $account
            )->discoverAddressBooks() as $addressBook
        ) {
            if (null !== $this->repository->findAddressBookByUri($addressBook->getUri(), $account->getId())) {
                continue;
            }
            $entity = CardDavAddressBook::new(
                $addressBook->getName(),
                $addressBook->getUri(),
                $account
            );
            $this->repository->persist($entity);
        }
    }
}
