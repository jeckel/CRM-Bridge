<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Stringable;
use Symfony\Component\Uid\Uuid;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
#[ORM\Entity(repositoryClass: ImapMessageRepository::class)]
#[ORM\UniqueConstraint(name: "account_message_ref", columns: ['imap_account_id', 'message_id'])]
#[ORM\UniqueConstraint(name: "folder_message_ref", columns: ['imap_account_id', 'folder', 'uid'])]
class ImapMessage implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'mail_id', type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'integer', nullable: false, options: ['unsigned' => true])]
    private int $uid;

    #[ORM\Column(length: 255, nullable: false)]
    private string $folder;

    #[ORM\Column(length: 255, nullable: false)]
    private string $messageId = '';

    #[ORM\Column]
    private DateTimeImmutable $date;

    #[ORM\Column(length: 255)]
    private string $subject = '';

    #[ORM\Column(length: 255)]
    private string $fromName = '';

    #[ORM\Column(length: 255)]
    private string $fromAddress = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $toString = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $headerRaw = '';

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

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function setId(UuidInterface|string $id): ImapMessage
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

    public function getToString(): string
    {
        return $this->toString;
    }

    public function setToString(string $toString): static
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

    public function setImapAccount(?ImapAccount $imapAccount): ImapMessage
    {
        $this->imapAccount = $imapAccount;
        return $this;
    }

    public function __toString()
    {
        return $this->getSubject();
    }
}
