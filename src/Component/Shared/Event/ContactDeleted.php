<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\Shared\Event;

use App\Component\Shared\Identity\ContactId;

final readonly class ContactDeleted
{
    public function __construct(
        public ContactId $contactId
    ) {}
}
