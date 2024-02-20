<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/02/2024
 */

declare(strict_types=1);

namespace App\Component\Shared\Event;

use App\Component\Shared\Identity\AccountId;
use App\Component\Shared\ValueObject\Email;
use DateTimeImmutable;

final readonly class AppointmentCreated implements Event
{
    public function __construct(
        public string $attendeeName,
        public Email $attendeeEmail,
        public AccountId $accountId,
        public DateTimeImmutable $requestDate,
        public DateTimeImmutable $appointmentDate,
        public string $appointmentSubject
    ) {}
}
