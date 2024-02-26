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
class ImapFolder
{
    #[ORM\Id]
    #[ORM\Column(name: 'imap_folder_id', type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private UuidInterface|string $id;

    #[ORM\Column(length: 180, nullable: false)]
    private string $name;

    #[ORM\Column(length: 180, nullable: false)]
    private string $slug;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $lastSyncUid = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?DateTimeImmutable $lastSyncDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'imap_parent_folder_id',
        referencedColumnName: 'imap_folder_id',
        nullable: true
    )]
    private ?ImapFolder $parent = null;

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

    public function setId(UuidInterface|string $id): ImapFolder
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ImapFolder
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): ImapFolder
    {
        $this->slug = $slug;
        return $this;
    }

    public function getLastSyncUid(): ?int
    {
        return $this->lastSyncUid;
    }

    public function setLastSyncUid(?int $lastSyncUid): ImapFolder
    {
        $this->lastSyncUid = $lastSyncUid;
        return $this;
    }

    public function getLastSyncDate(): ?DateTimeImmutable
    {
        return $this->lastSyncDate;
    }

    public function setLastSyncDate(?DateTimeImmutable $lastSyncDate): ImapFolder
    {
        $this->lastSyncDate = $lastSyncDate;
        return $this;
    }

    public function getParent(): ?ImapFolder
    {
        return $this->parent;
    }

    public function setParent(?ImapFolder $parent): ImapFolder
    {
        $this->parent = $parent;
        return $this;
    }

    public function getImapAccount(): ImapAccount
    {
        return $this->imapAccount;
    }

    public function setImapAccount(ImapAccount $imapAccount): ImapFolder
    {
        $this->imapAccount = $imapAccount;
        return $this;
    }
}
