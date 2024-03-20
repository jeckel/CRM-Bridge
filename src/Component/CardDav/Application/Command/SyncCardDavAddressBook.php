<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\Command;

use App\Component\Shared\Identity\CardDavAddressBookId;

readonly class SyncCardDavAddressBook
{
    public function __construct(
        public CardDavAddressBookId $addressBookId
    ) {}
}