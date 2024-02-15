<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Component\Shared\Event;

use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;
use DateTimeImmutable;

readonly class ContactCreated implements Event
{
    public function __construct(
        public ContactId $contactId,
        public ?Email $email,
        public DateTimeImmutable $occurredAt,
    ) {}
}
