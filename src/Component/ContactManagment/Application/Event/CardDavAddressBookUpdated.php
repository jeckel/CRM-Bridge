<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Event;

use App\Component\Shared\Identity\CardDavAddressBookId;

final readonly class CardDavAddressBookUpdated
{
    public function __construct(
        public CardDavAddressBookId $addressBookId
    ) {}
}
