<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 14:33
 */
declare(strict_types=1);

namespace App\Application\Async;

use App\Domain\Component\DirectCommunicationHub\Dto\IncomingMailDto;
use App\Domain\Component\DirectCommunicationHub\Service\IncomingMailRegisterer;
use App\Presentation\Async\Message\SyncMail;
use App\ValueObject\Email;
use DateTimeImmutable;
use Exception;
use PhpImap\Mailbox;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncMailHandler
{
    public function __construct(
        private Mailbox $mailbox,
        private IncomingMailRegisterer $mailRegisterer
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(SyncMail $syncMail): void
    {
        $mail = $this->mailbox->getMail($syncMail->mailId->id());
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
