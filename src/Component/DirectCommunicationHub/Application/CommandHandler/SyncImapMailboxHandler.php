<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\CommandHandler;

use App\Component\DirectCommunicationHub\Application\Command\SyncImapMailbox;
use App\Component\DirectCommunicationHub\Application\Service\MailboxSynchroniser;
use App\Infrastructure\Doctrine\Repository\ImapMailboxRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncImapMailboxHandler
{
    public function __construct(
        private ImapMailboxRepository $folderRepo,
        private MailboxSynchroniser $mailFolderSynchroniser
    ) {}

    public function __invoke(SyncImapMailbox $message): void
    {
        $folderEntity = $this->folderRepo->getById((string) $message->imapFolderId);
        $this->mailFolderSynchroniser->syncFolderEntity($folderEntity);
    }
}
