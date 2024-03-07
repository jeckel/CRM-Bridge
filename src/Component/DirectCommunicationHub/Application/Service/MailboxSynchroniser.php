<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/02/2024 19:00
 */
declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Service;

use App\Infrastructure\Doctrine\Entity\ImapMailbox;
use App\Infrastructure\Doctrine\Repository\ImapMailboxRepository;
use App\Infrastructure\Imap\ImapMailboxConnector;
use JeckelLab\Contract\Infrastructure\System\Clock;

readonly class MailboxSynchroniser
{
    public function __construct(
        private ImapMailboxRepository $folderRepo,
        private UpsertEmail $upsertEmail,
        private Clock $clock
    ) {}

    public function syncFolderEntity(ImapMailbox $mailbox): void
    {
        if (str_contains(strtolower($mailbox->getImapPath()), 'drafts')) {
            // Skip draft mailbox
            return;
        }
        $account = $mailbox->getImapAccount();
        $imapConnector = ImapMailboxConnector::fromImapAccount($account, $mailbox->getImapPath());
        $status = $imapConnector->statusMailbox();
        if (null === $mailbox->getUidValidity()) {
            // First sync
            $mailbox->setUidValidity($status->uidvalidity);
        }
        if ($mailbox->getUidValidity() !== $status->uidvalidity) {
            // Mailbox UID validity has changed, reset sync status
            $mailbox->setUidValidity($status->uidvalidity);
            $mailbox->setLastSyncUid(0);
        }
        if ($status->uidnext === ($mailbox->getLastSyncUid() + 1)) {
            // No new messages
            return;
        }

        for($uid = $mailbox->getLastSyncUid() + 1; $uid <= $status->uidnext; $uid++) {
            $imapMail = $imapConnector->getMail($uid);
            if (null === $imapMail) {
                continue;
            }
            if ($imapMail->headers->isDraft) {
                // Just skip draft emails
                continue;
            }
            $this->upsertEmail->upsert($imapMail, $account, $mailbox);
            $mailbox->setLastSyncUid($uid);
        }
        $mailbox->setLastSyncDate($this->clock->now());
        $this->folderRepo->persist($mailbox);
    }
}
