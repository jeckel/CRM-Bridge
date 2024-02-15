<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 14:30
 */
declare(strict_types=1);

namespace App\Presentation\Async\Message;

use App\Component\Shared\Identity\ImapConfigId;
use App\Component\Shared\Identity\MailId;

final readonly class SyncMail
{
    public function __construct(
        public MailId $mailId,
        public ImapConfigId $imapConfigId,
        public string $folder
    ) {}
}
