<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap;

use App\Identity\MailId;
use App\Infrastructure\Doctrine\Repository\MailRepository;
use App\Presentation\Async\Message\SyncMail;
use Exception;
use PhpImap\Mailbox;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class MailboxSynchronizer
{
    public function __construct(
        private Mailbox $mailbox,
        private MailRepository $mailRepository,
        private MessageBusInterface $messageBus,
    ) {}

    /**
     * @throws Exception
     */
    public function sync(): void
    {
        $mailsIds = $this->mailbox->searchMailbox('ALL');
        foreach($mailsIds as $mailId) {
            if (null !== $this->mailRepository->find($mailId)) {
                continue;
            }
            $this->messageBus->dispatch(new SyncMail(MailId::from($mailId)));
            //            $this->eventDispatcher->dispatch(
            //                new NewIncomingEmail(
            //                    MailId::from($mailId),
            //                    new Email($mail->fromAddress),
            //                    $date
            //                )
            //            );
        }
    }
}
