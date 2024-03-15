<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Command;

use App\Component\Shared\Identity\AddressBookId;

readonly class SyncCardDavAddressBook
{
    public function __construct(
        public AddressBookId $addressBookId
    ) {}
}
