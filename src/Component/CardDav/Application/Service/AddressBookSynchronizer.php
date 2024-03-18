<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\Service;

use App\Component\CardDav\Application\Port\RepositoryPort;
use App\Component\CardDav\Infrastructure\CardDav\CardDavClientProvider;
use App\Component\Shared\Identity\CardDavAddressBookId;

readonly class AddressBookSynchronizer
{
    public function __construct(
        private AddressBookSyncHandlerProvider $syncHandlerProvider,
        private CardDavClientProvider $clientProvider,
        private RepositoryPort $repository
    ) {}

    public function sync(CardDavAddressBookId $cardDavAddressBookId): void
    {
        $addressBook = $this->repository->getAddressBookById($cardDavAddressBookId);

        $syncToken = $this->clientProvider
            ->getClient($addressBook->account())
            ->sync(
                syncHandler: $this->syncHandlerProvider->getSyncHandler($addressBook->id()),
                addressBookUri: $addressBook->getUri(),
                lastSyncToken: $addressBook->getLastSyncToken()
            );
        $addressBook->setLastSyncToken($syncToken);
        $this->repository->persist($addressBook);
    }
}
