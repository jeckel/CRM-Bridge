<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use App\Application\CardDavObjectChanged;
use MStilkerich\CardDavClient\Services\SyncHandler;
use Sabre\VObject\Component\VCard;

class AddressBookSyncHandler implements SyncHandler
{
    public function __construct(
        private readonly CardDavObjectChanged $cardDavObjectChanged
    ) {}

    #[\Override]
    public function addressObjectChanged(string $uri, string $etag, ?VCard $card): void
    {
        if (null !== $card) {
            $this->cardDavObjectChanged->updateContactVCard($uri, $etag, $card);
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
