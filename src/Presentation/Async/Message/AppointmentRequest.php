<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Message;

use App\Component\Shared\Identity\AccountId;
use App\Presentation\Async\Message\AppointmentRequest\Attendee;
use DateTimeImmutable;

final readonly class AppointmentRequest implements Message
{
    /**
     * @param Attendee[] $attendees
     */
    public function __construct(
        public AccountId $accountId,
        public DateTimeImmutable $appointmentDate,
        public DateTimeImmutable $requestDate,
        public string $appointmentSubject,
        public array $attendees
    ) {}
}
