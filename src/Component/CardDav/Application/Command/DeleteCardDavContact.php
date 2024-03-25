<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\Command;

use App\Component\Shared\Identity\CardDavAccountId;
use App\Component\Shared\Identity\CardDavAddressBookId;

readonly class DeleteCardDavContact
{
    public function __construct(
        public CardDavAccountId $cardDavAccountId,
        public CardDavAddressBookId $addressBookId,
        public string $vCardUri
    ) {}
}
