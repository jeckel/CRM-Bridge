<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 14:33
 */
declare(strict_types=1);

namespace App\Application\AsyncHandler;

use App\Component\Shared\ValueObject\Email;
use App\Domain\Component\DirectCommunicationHub\Dto\IncomingMailDto;
use App\Domain\Component\DirectCommunicationHub\Service\IncomingMailRegisterer;
use App\Infrastructure\Doctrine\Repository\ImapConfigRepository;
use App\Infrastructure\Imap\ImapMailbox;
use App\Presentation\Async\Message\SyncMail;
use DateTimeImmutable;
use Exception;
use PhpImap\Mailbox;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncMailHandler
{
    public function __construct(
        private Mailbox $mailbox,
        private IncomingMailRegisterer $mailRegisterer,
        private ImapConfigRepository $imapConfigRepository
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(SyncMail $syncMail): void
    {
        $imapConfig = $this->imapConfigRepository->getById($syncMail->imapConfigId->id());
        $mailBox = ImapMailbox::fromImapConfig($imapConfig);
        $mail = $mailBox->getMail($syncMail->mailId->id(), $syncMail->folder);
//        dd($mail);
//        $mail = $this->mailbox->getMail($syncMail->mailId->id());
        $this->mailRegisterer->register(new IncomingMailDto(
            id: $syncMail->mailId,
            messageId: $mail->messageId,
            date: new DateTimeImmutable($mail->date),
            subject: $mail->subject,
            fromName: $mail->fromName,
            fromAddress: new Email($mail->fromAddress),
            toString: $mail->toString,
            textPlain: $mail->textPlain,
            textHtml: $mail->textHtml
        ));
    }
}
