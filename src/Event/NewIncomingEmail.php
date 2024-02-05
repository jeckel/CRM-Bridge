<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Event;

use App\Identity\MailId;
use App\ValueObject\Email;

final readonly class NewIncomingEmail implements Event
{
    public function __construct(
        public MailId $mailId,
        public Email $email,
    ) {}
}
