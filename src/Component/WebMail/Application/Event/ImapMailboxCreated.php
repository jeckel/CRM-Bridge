<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 12:09
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\Event;

use App\Component\Shared\Identity\ImapMailboxId;
use JeckelLab\Contract\Domain\Event\Event;

readonly class ImapMailboxCreated implements Event
{
    public function __construct(public ImapMailboxId $mailboxId) {}
}
