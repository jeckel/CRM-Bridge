<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Message\AppointmentRequest;

use App\Component\Shared\ValueObject\Email;

class Attendee
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public string $displayName,
        public Email $email,
        public ?string $phoneNumber,
    ) {}
}
