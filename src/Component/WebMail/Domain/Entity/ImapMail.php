<?php

namespace App\Component\WebMail\Domain\Entity;

use App\Component\Shared\Identity\ImapMailboxId;
use App\Component\Shared\Identity\ImapMailId;
use App\Component\Shared\ValueObject\IncomingEmailType;
use App\Component\WebMail\Application\Dto\ImapMailDto;
use DateTimeImmutable;
use LogicException;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class ImapMail
{
    private IncomingEmailType $emailType = IncomingEmailType::UNDEFINED; /** @phpstan-ignore-line  */
    private bool $isTreated = false; /** @phpstan-ignore-line  */
    private ?DateTimeImmutable $treatedAt = null; /** @phpstan-ignore-line  */
    private ?ImapMailAuthor $author = null; /** @phpstan-ignore-line  */

    /**
     * @param array<string, string> $spamHeaders
     * @suppressWarnings(PHPMD.ExcessiveParameterList)
     */
    private function __construct(
        public readonly ImapMailId $id,
        private int $imapUid, /** @phpstan-ignore-line  */
        private string $imapPath, /** @phpstan-ignore-line  */
        public readonly string $messageId,
        public readonly string $messageUniqueId,
        public readonly DateTimeImmutable $date,
        public readonly string $subject,
        public readonly string $fromName,
        public readonly string $fromAddress,
        public readonly string $deliveredTo,
        public readonly?string $toString,
        public readonly string $headerRaw,
        public readonly bool $hasAttachment,
        private bool $isSpam, /** @phpstan-ignore-line  */
        public readonly int $spamScore,
        public readonly array $spamHeaders,
        public readonly?string $textPlain,
        public readonly?string $textHtml,
        public readonly ImapAccount $account,
        private ImapMailbox $mailbox /** @phpstan-ignore-line  */
    ) {}

    public static function fromImapMailDto(ImapMailDto $dto, ImapMailbox $mailbox): self
    {
        $mail = new self(
            id: ImapMailId::new(),
            imapUid: $dto->uid,
            imapPath: $dto->imapPath,
            messageId: $dto->messageId,
            messageUniqueId: $dto->messageUniqueId,
            date: new DateTimeImmutable($dto->date ?? throw new LogicException('Date can not be null')),
            subject: $dto->subject ?? '',
            fromName: $dto->fromName ?? '',
            fromAddress: $dto->fromAddress ?? '',
            deliveredTo: '',
//            deliveredTo: $dto->deliveredTo,
            toString: $dto->toString,
            headerRaw: $dto->headersRaw,
            hasAttachment: $dto->hasAttachments,
            isSpam: false,
            spamScore: 0,
            spamHeaders: [],
//            isSpam: $dto->isSpam,
//            spamScore: $dto->spamScore,
//            spamHeaders: $dto->spamHeaders,
            textPlain: $dto->textPlain,
            textHtml: $dto->textHtml,
            account: $mailbox->account(),
            mailbox: $mailbox
        );
        return $mail;
    }
}
