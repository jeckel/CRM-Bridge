<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\Service;

use App\Component\CardDav\Infrastructure\CardDav\VCard\ContactVCard;
use App\Component\Contact\Application\Command\DeleteInternalContact;
use App\Component\Contact\Application\Command\UpsertContactVCard;
use App\Component\ContactManagment\Application\Command\UpsertInternalContact;
use App\Component\Shared\Identity\CardDavAddressBookId;
use MStilkerich\CardDavClient\Services\SyncHandler;
use Override;
use Sabre\VObject\Component\VCard;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class AddressBookSyncHandler implements SyncHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private CardDavAddressBookId $addressBookId
    ) {}

    #[Override]
    public function addressObjectChanged(string $uri, string $etag, ?VCard $card): void
    {
        if (null === $card) {
            return;
        }
        $this->messageBus->dispatch(
            new UpsertContactVCard(
                vCard: new ContactVCard($uri, $card),
                addressBookId: $this->addressBookId,
                vCardEtag: $etag
            )
        );
    }

    #[Override]
    public function addressObjectDeleted(string $uri): void
    {
        $this->messageBus->dispatch(
            new DeleteInternalContact(
                vCardUri: $uri
            )
        );
    }

    #[Override]
    public function getExistingVCardETags(): array
    {
        return [];
    }

    #[Override]
    public function finalizeSync(): void {}
}