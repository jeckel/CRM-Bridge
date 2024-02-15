<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 14:33
 */
declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\MessageHandler;

use App\Component\DirectCommunicationHub\Application\Helper\MailContextManager;
use App\Component\DirectCommunicationHub\Domain\Dto\IncomingMailDto;
use App\Component\DirectCommunicationHub\Domain\Service\IncomingMailRegisterer;
use App\Component\Shared\Helper\ContextManager;
use App\Component\Shared\Identity\AccountId;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Repository\ImapConfigRepository;
use App\Infrastructure\Imap\ImapMailbox;
use App\Presentation\Async\Message\SyncMail;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncMailHandler
{
    public function __construct(
        private IncomingMailRegisterer $mailRegisterer,
        private ImapConfigRepository $imapConfigRepository,
        private ContextManager $context,
        private MailContextManager $mailContext
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(SyncMail $syncMail): void
    {
        $imapConfig = $this->imapConfigRepository->getById($syncMail->imapConfigId->id());
        $mailBox = ImapMailbox::fromImapConfig($imapConfig);
        $this->context->setAccountId(AccountId::from((string) $imapConfig->getAccountOrFail()->getId()));
        $this->mailContext->setImapConfigId($syncMail->imapConfigId);

        $mail = $mailBox->getMail($syncMail->mailId->id(), $syncMail->folder);
        $this->mailRegisterer->register(new IncomingMailDto(
            id: $syncMail->mailId,
            messageId: $mail->messageId,
            date: new DateTimeImmutable($mail->date),
            subject: $mail->subject,
            fromName: $mail->fromName,
            fromAddress: new Email($mail->fromAddress),
            toString: $mail->toString,
            folder: $syncMail->folder,
            textPlain: $mail->textPlain,
            textHtml: $mail->textHtml
        ));
    }
}
