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
use JeckelLab\Contract\Infrastructure\System\Clock;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncMailboxHandler
{
    public function __construct(
        private ImapPort $imap,
        private RepositoryPort $repository,
        private Clock $clock
    ) {}
    public function __invoke(SyncMailbox $command): void
    {
        $mailbox = $this->repository->getMailboxById($command->mailboxId);
        $status = $this->imap->getStatus($mailbox->account(), $mailbox->imapPath);

        $mailbox->updateUidValidity($status->uidvalidity, $status->minUid);
        if (!$mailbox->requireSync($status->uidnext)) {
            return;
        }

        foreach(range($mailbox->lastSyncUid(), ($status->uidnext - 1)) as $uid) {
            $mail = $this->imap->getMail($mailbox->account(), $mailbox->imapPath, $uid);
            if (null === $mail) {
                continue;
            }
            if ($mail->headers->isDraft) {
                // Just skip draft emails
                continue;
            }
            $found = $this->repository->findMailByUniqueMessageId($mail->messageUniqueId);
            if (null !== $found) {
                // @todo : Update existing message (mailbox changed?)
                continue;
            }
            $entity = ImapMail::fromImapMailDto($mail, $mailbox);
            $this->repository->persist($entity);
        }
        $mailbox->updateSyncStatus($status, isset($uid) ? $uid : null, $this->clock->now());
        $this->repository->persist($mailbox);
    }
}
