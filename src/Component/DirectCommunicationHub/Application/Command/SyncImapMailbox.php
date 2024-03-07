<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Command;

use App\Component\Shared\Identity\ImapMailboxId;

final readonly class SyncImapMailbox
{
    public function __construct(public ImapMailboxId $imapFolderId) {}
}
