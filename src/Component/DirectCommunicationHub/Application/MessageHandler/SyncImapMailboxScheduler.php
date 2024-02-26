<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\MessageHandler;

use App\Component\Shared\Identity\ImapFolderId;
use App\Infrastructure\Doctrine\Entity\ImapFolder;
use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use App\Infrastructure\Doctrine\Repository\ImapFolderRepository;
use App\Infrastructure\Imap\ImapMailbox;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

use function App\new_uuid;
use function App\slug;

#[AsCronTask(
    expression: '*/5 * * * *',
    method: 'onSchedule'
)]
readonly class SyncImapMailboxScheduler
{
    public function __construct(
        private ImapAccountRepository $accountRepo,
        private ImapFolderRepository $folderRepo,
        private MessageBusInterface $messageBus
    ) {}

    public function onSchedule(): void
    {
        foreach($this->accountRepo->findAll() as $imapAccount) {
            $folders = ImapMailbox::fromImapAccount($imapAccount)->listFolders();
            foreach($folders as $folder) {
                $folderEntity = $this->folderRepo->findOneBy([
                    'imapAccount' => $imapAccount,
                    'name' => $folder->shortpath
                ]);
                if (null === $folderEntity) {
                    $folderEntity = (new ImapFolder())
                        ->setId(new_uuid())
                        ->setImapAccount($imapAccount)
                        ->setName($folder->shortpath)
                        ->setSlug(slug($folder->shortpath));
                    $this->folderRepo->persist($folderEntity);
                }

                $this->messageBus->dispatch(
                    new SyncImapFolderMessage(ImapFolderId::from((string) $folderEntity->getId()))
                );
            }
        }
    }
}
