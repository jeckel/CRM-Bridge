<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Application;

use App\Application\Dto\VCardDto;
use App\Domain\Component\ContactManagment\Service\ContactVCardUpdater;
use Sabre\VObject\Component\VCard;

readonly class CardDavObjectChanged
{
    public function __construct(
        private ContactVCardUpdater $VCardUpdater
    ) {}

    public function updateContactVCard(string $uri, string $etag, VCard $card): void
    {
        $this->VCardUpdater->sync(new VCardDto($uri, $etag, $card));
    }
}
