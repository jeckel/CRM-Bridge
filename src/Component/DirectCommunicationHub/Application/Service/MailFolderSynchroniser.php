<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/02/2024 19:00
 */
declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Service;

use App\Component\Shared\Identity\ImapConfigId;
use App\Component\Shared\Identity\MailId;
use App\Infrastructure\Doctrine\Entity\ImapConfig;
use App\Infrastructure\Imap\ImapMailbox;
use App\Presentation\Async\Message\SyncMail;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class MailFolderSynchroniser
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    public function syncFolder(ImapConfig $imapConfig, string $folder): void
    {
        $mailbox = ImapMailbox::fromImapConfig($imapConfig);
        $mailsIds = $mailbox->searchFolder($folder);
        foreach($mailsIds as $mailId) {
            $this->messageBus->dispatch(new SyncMail(
                mailId: MailId::from($mailId),
                imapConfigId: ImapConfigId::from((string) $imapConfig->getId()),
                folder: $folder
            ));
        }
    }
}
