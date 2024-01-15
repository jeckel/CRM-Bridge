<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Event;

use App\Identity\ContactId;
use DateTimeImmutable;

readonly class ContactCreated implements Event
{
    public function __construct(
        public ContactId $contactId,
        public DateTimeImmutable $occurredAt,
    ) {}
}
