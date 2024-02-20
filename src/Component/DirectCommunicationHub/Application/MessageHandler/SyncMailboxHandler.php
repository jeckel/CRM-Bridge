<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\MessageHandler;

use App\Component\DirectCommunicationHub\Application\Service\MailFolderSynchroniser;
use App\Infrastructure\Doctrine\Repository\ImapConfigRepository;
use App\Presentation\Async\Message\SyncMailbox;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsMessageHandler]
#[AsCronTask(
    expression: '*/5 * * * *',
    method: 'onSchedule'
)]
readonly class SyncMailboxHandler
{
    public function __construct(
        private MailFolderSynchroniser $mailFolderSynchroniser,
        private ImapConfigRepository $repository
    ) {}

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(SyncMailbox $message): void
    {
        $imapConfig = $this->repository->getById($message->imapConfigId->id());
        foreach($imapConfig->getFolders() as $folder) {
            $this->mailFolderSynchroniser->syncFolder(
                $imapConfig,
                $folder
            );
        }
    }

    public function onSchedule(): void
    {
        foreach($this->repository->findAll() as $imapConfig) {
            foreach($imapConfig->getFolders() as $folder) {
                $this->mailFolderSynchroniser->syncFolder(
                    $imapConfig,
                    $folder
                );
            }
        }
    }
}
