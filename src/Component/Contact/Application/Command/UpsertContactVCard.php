<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Application\Command;

use App\Component\CardDav\Infrastructure\CardDav\VCard\ContactVCard;
use App\Component\Shared\Identity\CardDavAddressBookId;

readonly class UpsertContactVCard
{
    public function __construct(
        public ContactVCard $vCard,
        public CardDavAddressBookId $addressBookId,
        public string $vCardEtag
    ) {}
}
