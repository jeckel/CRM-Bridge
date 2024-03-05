<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Doctrine\Repository\ImapFolderRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: ImapFolderRepository::class)]
#[ORM\UniqueConstraint(name: "folder_unique_ref", columns: ['imap_account_id', 'slug'])]
class ImapMailbox
{
    #[ORM\Id]
    #[ORM\Column(name: 'imap_folder_id', type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private UuidInterface|string $id;

    #[ORM\Column(length: 180, nullable: false)]
    private string $name;

    #[ORM\Column(length: 255, nullable: false)]
    private string $imapPath;

    #[ORM\Column(length: 180, nullable: false)]
    private string $slug;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $lastSyncUid = null;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $flags = 0;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $messages = 0;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $recent = 0;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $unseen = 0;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $uidNext = 0;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?DateTimeImmutable $lastSyncDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'imap_parent_folder_id',
        referencedColumnName: 'imap_folder_id',
        nullable: true
    )]
    private ?ImapMailbox $parent = null;

    #[ORM\ManyToOne(
        cascade: ['persist'],
        inversedBy: 'folders'
    )]
    #[ORM\JoinColumn(
        name: 'imap_account_id',
        referencedColumnName: 'imap_account_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private ImapAccount $imapAccount;

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function setId(UuidInterface|string $id): ImapMailbox
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ImapMailbox
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): ImapMailbox
    {
        $this->slug = $slug;
        return $this;
    }

    public function getImapPath(): string
    {
        return $this->imapPath;
    }

    public function setImapPath(string $imapPath): self
    {
        $this->imapPath = $imapPath;
        return $this;
    }

    public function getLastSyncUid(): ?int
    {
        return $this->lastSyncUid;
    }

    public function setLastSyncUid(?int $lastSyncUid): ImapMailbox
    {
        $this->lastSyncUid = $lastSyncUid;
        return $this;
    }

    public function getLastSyncDate(): ?DateTimeImmutable
    {
        return $this->lastSyncDate;
    }

    public function setLastSyncDate(?DateTimeImmutable $lastSyncDate): ImapMailbox
    {
        $this->lastSyncDate = $lastSyncDate;
        return $this;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function setFlags(int $flags): self
    {
        $this->flags = $flags;
        return $this;
    }

    public function getMessages(): int
    {
        return $this->messages;
    }

    public function setMessages(int $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    public function getRecent(): int
    {
        return $this->recent;
    }

    public function setRecent(int $recent): self
    {
        $this->recent = $recent;
        return $this;
    }

    public function getUnseen(): int
    {
        return $this->unseen;
    }

    public function setUnseen(int $unseen): self
    {
        $this->unseen = $unseen;
        return $this;
    }

    public function getUidNext(): int
    {
        return $this->uidNext;
    }

    public function setUidNext(int $uidNext): self
    {
        $this->uidNext = $uidNext;
        return $this;
    }

    public function getParent(): ?ImapMailbox
    {
        return $this->parent;
    }

    public function setParent(?ImapMailbox $parent): ImapMailbox
    {
        $this->parent = $parent;
        return $this;
    }

    public function getImapAccount(): ImapAccount
    {
        return $this->imapAccount;
    }

    public function setImapAccount(ImapAccount $imapAccount): ImapMailbox
    {
        $this->imapAccount = $imapAccount;
        return $this;
    }
}
