<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SensitiveParameter;
use Stringable;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
#[ORM\Entity(repositoryClass: ImapAccountRepository::class)]
class ImapAccount implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'imap_account_id', type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private UuidInterface|string $id;

    #[ORM\Column(length: 180, nullable: false)]
    private string $name;

    #[ORM\Column(length: 255, nullable: false)]
    private string $uri;

    #[ORM\Column(length: 255, nullable: false)]
    private string $login;

    #[ORM\Column(length: 255, nullable: false)]
    private string $password;

    /**
     * @var Collection<int, ImapMailbox> $mailboxes
     */
    #[ORM\OneToMany(
        targetEntity: ImapMailbox::class,
        mappedBy: 'imapAccount',
        cascade: ['persist']
    )]
    private Collection $mailboxes;

    public function __construct()
    {
        $this->mailboxes = new ArrayCollection();
    }

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function setId(UuidInterface|string $id): ImapAccount
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ImapAccount
    {
        $this->name = $name;
        return $this;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): ImapAccount
    {
        $this->uri = $uri;
        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): ImapAccount
    {
        $this->login = $login;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(#[SensitiveParameter] string $password): ImapAccount
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return Collection<int, ImapMailbox>
     */
    public function getMailboxes(): Collection
    {
        return $this->mailboxes;
    }

    public function __toString()
    {
        return $this->name;
    }
}
