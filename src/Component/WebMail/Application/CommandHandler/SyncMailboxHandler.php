<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 16:30
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\CommandHandler;

use App\Component\WebMail\Application\Command\SyncMailbox;
use App\Component\WebMail\Application\Port\ImapPort;
use App\Component\WebMail\Application\Port\RepositoryPort;
use App\Component\WebMail\Domain\Entity\ImapMail;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncMailboxHandler
{
    public function __construct(
        private ImapPort $imap,
        private RepositoryPort $repository
    ) {}
    public function __invoke(SyncMailbox $command): void
    {
        $mailbox = $this->repository->getMailboxById($command->mailboxId);
        $status = $this->imap->getStatus($mailbox->account(), $mailbox->imapPath);

        $mailbox->updateUidValidity($status->uidvalidity, $status->minUid);
        if (!$mailbox->requireSync($status->uidnext)) {
            return;
        }

        foreach(range($mailbox->lastSyncUid(), $status->uidnext) as $uid) {
            $mail = $this->imap->getMail($mailbox->account(), $mailbox->imapPath, $uid);
            if (null === $mail) {
                continue;
            }
            $entity = ImapMail::fromImapMailDto($mail, $mailbox);
            $this->repository->persistMail($entity);
            dd($mail);
        }
    }
}
