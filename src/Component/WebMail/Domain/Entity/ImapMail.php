<?php

namespace App\Component\WebMail\Domain\Entity;

use App\Component\Shared\Identity\ImapMailboxId;
use App\Component\Shared\ValueObject\IncomingEmailType;
use DateTimeImmutable;

class ImapMail
{
    public readonly ImapMailboxId $id;
    private int $imapUid;
    private string $imapPath;
    private string $messageId = '';
    private string $messageUniqueId = '';
    private DateTimeImmutable $date;
    private string $subject = '';
    private string $fromName = '';
    private string $fromAddress = '';
    private string $deliveredTo = '';
    private ?string $toString = null;
    private string $headerRaw = '';
    private bool $hasAttachment = false;
    private IncomingEmailType $emailType = IncomingEmailType::UNDEFINED;
    private bool $isSpam = false;
    private int $spamScore = 0;
    private array $spamHeaders = [];
    private ?string $textPlain = null;
    private ?string $textHtml = null;
    private bool $isTreated = false;
    private ?DateTimeImmutable $treatedAt = null;

    private ImapAccount $account;
    private ImapMailbox $mailbox;
    private ?ImapMailAuthor $author;
}
