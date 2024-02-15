<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Message;

use App\Component\Shared\Identity\ImapConfigId;

final readonly class SyncMailbox
{
    public function __construct(
        public ImapConfigId $imapConfigId
    ) {}
}
