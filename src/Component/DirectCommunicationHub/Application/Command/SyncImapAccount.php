<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/03/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Command;

use App\Component\Shared\Identity\ImapAccountId;

final readonly class SyncImapAccount
{
    public function __construct(public ImapAccountId $imapAccountId) {}
}
