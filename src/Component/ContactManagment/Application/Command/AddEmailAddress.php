<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Command;

use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;

readonly class AddEmailAddress
{
    public function __construct(
        public Email $emailAddress,
        public ContactId $contactId
    ) {}
}
