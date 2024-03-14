<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 14/03/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Command;

use App\Component\ContactManagment\Application\Dto\ContactDto;
use App\Component\Shared\Identity\AddressBookId;

final readonly class CreateContact
{
    public function __construct(
        public ContactDto $contactData,
        public AddressBookId $addressBookId
    ) {}
}
