<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\MessageHandler;

use App\Component\Shared\Identity\ImapFolderId;

final readonly class SyncImapFolderMessage
{
    public function __construct(public ImapFolderId $imapFolderId) {}
}
