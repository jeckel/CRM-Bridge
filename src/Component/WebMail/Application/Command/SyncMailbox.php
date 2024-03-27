<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 16:28
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\Command;

use App\Component\Shared\Identity\ImapMailboxId;

readonly class SyncMailbox
{
    public function __construct(
        public ImapMailboxId $mailboxId,
    ) {}
}
