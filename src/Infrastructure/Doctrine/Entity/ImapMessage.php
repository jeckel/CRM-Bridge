<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\ValueObject\IncomingEmailType;
use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;
use Stringable;
use Symfony\Component\Uid\Uuid;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
#[ORM\Entity(repositoryClass: ImapMessageRepository::class)]
#[ORM\UniqueConstraint(name: "account_message_ref", columns: ['imap_account_id', 'message_unique_id'])]
class ImapMessage implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'mail_id', type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: TYPES::INTEGER, nullable: false, options: ['unsigned' => true])]
    private int $uid;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $folder;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $messageId = '';

    #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: false)]
    private string $messageUniqueId = '';

    #[ORM\Column]
    private DateTimeImmutable $date;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $subject = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $fromName = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $fromAddress = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $deliveredTo = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $toString = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $headerRaw = '';

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $hasAttachment = false;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: false, enumType: IncomingEmailType::class)]
    private IncomingEmailType $emailType = IncomingEmailType::UNDEFINED;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isSpam = false;

    #[ORM\Column(type: Types::INTEGER)]
    private int $spamScore = 0;

    /** @var array<string, string> */
    #[ORM\Column(type: Types::JSON)]
    private array $spamHeaders = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textPlain = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textHtml = null;

    #[ORM\ManyToOne(inversedBy: 'mails')]
    #[ORM\JoinColumn(
        name: 'contact_id',
        referencedColumnName: 'contact_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?Contact $contact;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'imap_account_id',
        referencedColumnName: 'imap_account_id',
        nullable: false
    )]
    private ?ImapAccount $imapAccount = null;

    #[ORM\Column(type: TYPES::BOOLEAN, nullable: false)]
    private bool $isTreated = false;

    #[ORM\Column(type: TYPES::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $treatedAt = null;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): ImapMessage
    {
        $this->id = $id;
        return $this;
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function setUid(int $uid): ImapMessage
    {
        $this->uid = $uid;
        return $this;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function setFolder(string $folder): ImapMessage
    {
        $this->folder = $folder;
        return $this;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function setMessageId(string $messageId): static
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getMessageUniqueId(): string
    {
        return $this->messageUniqueId;
    }

    public function setMessageUniqueId(string $messageUniqueId): ImapMessage
    {
        $this->messageUniqueId = $messageUniqueId;
        return $this;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getFromName(): string
    {
        return $this->fromName;
    }

    public function setFromName(string $fromName): static
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function getFromAddress(): string
    {
        return $this->fromAddress;
    }

    public function setFromAddress(string $fromAddress): static
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    public function getDeliveredTo(): string
    {
        return $this->deliveredTo;
    }

    public function setDeliveredTo(string $deliveredTo): ImapMessage
    {
        $this->deliveredTo = $deliveredTo;
        return $this;
    }

    public function getToString(): ?string
    {
        return $this->toString;
    }

    public function setToString(?string $toString): static
    {
        $this->toString = $toString;

        return $this;
    }

    public function getHeaderRaw(): string
    {
        return $this->headerRaw;
    }

    public function setHeaderRaw(string $headerRaw): ImapMessage
    {
        $this->headerRaw = $headerRaw;
        return $this;
    }

    public function getTextPlain(): ?string
    {
        return $this->textPlain;
    }

    public function setTextPlain(?string $textPlain): static
    {
        $this->textPlain = $textPlain;

        return $this;
    }

    public function getTextHtml(): ?string
    {
        return $this->textHtml;
    }

    public function setTextHtml(?string $textHtml): static
    {
        $this->textHtml = $textHtml;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): ImapMessage
    {
        $this->contact = $contact;
        return $this;
    }

    public function getImapAccount(): ?ImapAccount
    {
        return $this->imapAccount;
    }

    public function getImapAccountOrFail(): ImapAccount
    {
        if (null === $this->imapAccount) {
            throw new RuntimeException('ImapAccount not set');
        }

        return $this->imapAccount;
    }

    public function setImapAccount(?ImapAccount $imapAccount): ImapMessage
    {
        $this->imapAccount = $imapAccount;
        return $this;
    }

    public function isTreated(): bool
    {
        return $this->isTreated;
    }

    public function setIsTreated(bool $isTreated): ImapMessage
    {
        $this->isTreated = $isTreated;
        return $this;
    }

    public function getTreatedAt(): ?DateTimeImmutable
    {
        return $this->treatedAt;
    }

    public function setTreatedAt(?DateTimeImmutable $treatedAt): ImapMessage
    {
        $this->treatedAt = $treatedAt;
        return $this;
    }

    public function isHasAttachment(): bool
    {
        return $this->hasAttachment;
    }

    public function setHasAttachment(bool $hasAttachment): ImapMessage
    {
        $this->hasAttachment = $hasAttachment;
        return $this;
    }

    public function getEmailType(): IncomingEmailType
    {
        return $this->emailType;
    }

    public function setEmailType(IncomingEmailType $emailType): ImapMessage
    {
        $this->emailType = $emailType;
        return $this;
    }

    public function isSpam(): bool
    {
        return $this->isSpam;
    }

    public function setIsSpam(bool $isSpam): ImapMessage
    {
        $this->isSpam = $isSpam;
        return $this;
    }

    public function getSpamScore(): int
    {
        return $this->spamScore;
    }

    public function setSpamScore(int $spamScore): ImapMessage
    {
        $this->spamScore = $spamScore;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getSpamHeaders(): array
    {
        return $this->spamHeaders;
    }

    /**
     * @param string[] $spamHeaders
     */
    public function setSpamHeaders(array $spamHeaders): ImapMessage
    {
        $this->spamHeaders = $spamHeaders;
        return $this;
    }

    public function __toString()
    {
        return $this->getSubject();
    }
}
