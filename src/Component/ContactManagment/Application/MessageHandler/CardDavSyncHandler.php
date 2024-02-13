<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\MessageHandler;

use App\Component\ContactManagment\Application\Dto\VCardDto;
use App\Component\ContactManagment\Domain\Service\ContactVCardUpdater;
use App\Component\Shared\Identity\AccountId;
use App\Component\Shared\Identity\AddressBookId;
use MStilkerich\CardDavClient\Services\SyncHandler;
use Sabre\VObject\Component\VCard;

class CardDavSyncHandler implements SyncHandler
{
    private AccountId $accountId;
    private AddressBookId $addressBookId;

    public function __construct(
        private readonly ContactVCardUpdater $VCardUpdater
    ) {}

    public function setAccountId(AccountId $accountId): CardDavSyncHandler
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function setAddressBookId(AddressBookId $addressBookId): CardDavSyncHandler
    {
        $this->addressBookId = $addressBookId;
        return $this;
    }

    #[\Override]
    public function addressObjectChanged(string $uri, string $etag, ?VCard $card): void
    {
        if (null !== $card) {
            $this->VCardUpdater->sync(
                vCard: new VCardDto($uri, $etag, $card),
                accountId: $this->accountId,
                addressBookId: $this->addressBookId
            );
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[\Override]
    public function addressObjectDeleted(string $uri): void
    {
        // TODO: Implement addressObjectDeleted() method.
    }

    #[\Override]
    public function getExistingVCardETags(): array
    {
        return [];
    }

    #[\Override]
    public function finalizeSync(): void
    {
        // TODO: Implement finalizeSync() method.
    }
}
