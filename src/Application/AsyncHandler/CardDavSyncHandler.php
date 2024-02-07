<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Application\AsyncHandler;

use App\Application\Dto\VCardDto;
use App\Domain\Component\ContactManagment\Service\ContactVCardUpdater;
use MStilkerich\CardDavClient\Services\SyncHandler;
use Sabre\VObject\Component\VCard;

readonly class CardDavSyncHandler implements SyncHandler
{
    public function __construct(
        private ContactVCardUpdater $VCardUpdater
    ) {}

    #[\Override]
    public function addressObjectChanged(string $uri, string $etag, ?VCard $card): void
    {
        if (null !== $card) {
            $this->VCardUpdater->sync(new VCardDto($uri, $etag, $card));
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
