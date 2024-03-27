<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:38
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\Command;

use App\Component\Shared\Identity\ImapAccountId;

readonly class SyncImapAccount
{
    public function __construct(
        public ImapAccountId $accountId,
    ) {}
}
