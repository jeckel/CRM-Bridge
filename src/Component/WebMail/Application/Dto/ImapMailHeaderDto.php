<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 04/03/2024
 */

declare(strict_types=1);

namespace App\Component\WebMail\Application\Dto;

readonly class ImapMailHeaderDto
{
    /**
     * @param array<string, string|null> $to
     * @param array<string, string|null> $cc
     * @param array<string, string|null> $bcc
     * @param array<string, string|null> $replyTo
     * @suppressWarnings(PHPMD.ExcessiveParameterList)
     * @suppressWarnings(PHPMD.ShortVariable)
     * @suppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function __construct(
        public ?int $id, // The IMAP message ID - not the "Message-ID:"-header of the email
        public ?string $imapPath,
        public ?string $mailboxFolder,
        public ?string $date,
        public string $headersRaw,
        public ?object $headers,
        public ?string $mimeVersion,
        public ?string $xVirusScanned,
        public ?string $organization,
        public ?string $contentType,
        public ?string $xMailer,
        public ?string $contentLanguage,
        public ?string $xSenderIp,
        public ?string $priority,
        public ?string $importance,
        public ?string $sensitivity,
        public ?string $autoSubmitted,
        public ?string $precedence,
        public ?string $failedRecipients,
        public ?string $subject,
        public ?string $fromHost,
        public ?string $fromName,
        public ?string $fromAddress,
        public ?string $senderHost,
        public ?string $senderName,
        public ?string $senderAddress,
        public ?string $xOriginalTo,
        public ?string $toString,
        public ?string $ccString,
        public ?string $messageId,
        public bool $isSeen = false,
        public bool $isAnswered = false,
        public bool $isRecent = false,
        public bool $isFlagged = false,
        public bool $isDeleted = false,
        public bool $isDraft = false,
        public array $to = [],
        public array $cc = [],
        public array $bcc = [],
        public array $replyTo = [],
    ) {}
}
