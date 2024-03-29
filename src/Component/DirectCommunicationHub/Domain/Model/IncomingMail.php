<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:14
 */
declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Domain\Model;

use App\Component\DirectCommunicationHub\Domain\Dto\IncomingMailDto;
use App\Component\Shared\DomainTrait\ReadPropertyTrait;
use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\Identity\MailId;
use App\Component\Shared\ValueObject\Email;
use DateTimeImmutable;

/**
 * @property-read MailId $mailId
 * @property-read string $messageId
 * @property-read string $folder
 * @property-read DateTimeImmutable $date
 * @property-read string $subject
 * @property-read string $fromName
 * @property-read EMail $fromAddress
 * @property-read string $toString
 * @property-read ?string $textPlain
 * @property-read ?string $textHtml
 * @property-read ?Author $author
 */
class IncomingMail
{
    use ReadPropertyTrait;

    /**
     * @suppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        protected MailId $mailId,
        protected string $messageId,
        protected string $folder,
        protected DateTimeImmutable $date,
        protected string $subject,
        protected string $fromName,
        protected EMail $fromAddress,
        protected string $toString,
        protected ?string $textPlain = null,
        protected ?string $textHtml = null,
        protected ?Author $author = null
    ) {}

    public static function fromIncomingMailDto(IncomingMailDto $incomingMail): self
    {
        return new self(
            $incomingMail->id,
            $incomingMail->messageId,
            $incomingMail->folder,
            $incomingMail->date,
            $incomingMail->subject,
            $incomingMail->fromName,
            $incomingMail->fromAddress,
            $incomingMail->toString,
            $incomingMail->textPlain,
            $incomingMail->textHtml
        );
    }

    public function linkToAuthor(?Author $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function authorId(): ?ContactId
    {
        return $this->author?->id;
    }
}
