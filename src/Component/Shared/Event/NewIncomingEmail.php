<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\Shared\Event;

use App\Component\Shared\Identity\ImapMailId;
use App\Component\Shared\ValueObject\Email;
use DateTimeImmutable;

final readonly class NewIncomingEmail implements Event
{
    public function __construct(
        public ImapMailId $mailId,
        public Email $email,
        public DateTimeImmutable $sendAt,
    ) {}
}
