<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\MessageHandler;

use App\Component\DirectCommunicationHub\Application\Service\MailFolderSynchroniser;
use App\Infrastructure\Doctrine\Repository\ImapFolderRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncImapFolderHandler
{
    public function __construct(
        private ImapFolderRepository $folderRepo,
        private MailFolderSynchroniser $mailFolderSynchroniser
    ) {}

    public function __invoke(SyncImapFolderMessage $message): void
    {
        $folderEntity = $this->folderRepo->getById((string) $message->imapFolderId);
        $this->mailFolderSynchroniser->syncFolderEntity($folderEntity);
    }
}
