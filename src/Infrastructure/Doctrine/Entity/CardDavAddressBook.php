<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Stringable;

#[ORM\Entity(repositoryClass: CardDavAddressBookRepository::class)]
class CardDavAddressBook implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'card_dav_address_book_id', type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private UuidInterface|string $id;

    #[ORM\Column(length: 180, nullable: false)]
    private string $name;

    #[ORM\Column(length: 255, nullable: false)]
    private string $uri;

    #[ORM\Column()]
    private bool $enabled = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastSyncToken = null;

    #[ORM\ManyToOne(
        inversedBy: 'addressBooks'
    )]
    #[ORM\JoinColumn(
        name: 'card_dav_config_id',
        referencedColumnName: 'card_dav_config_id',
        nullable: false
    )]
    private ?CardDavConfig $cardDavConfig = null;

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function setId(UuidInterface|string $id): CardDavAddressBook
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): CardDavAddressBook
    {
        $this->name = $name;
        return $this;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): CardDavAddressBook
    {
        $this->uri = $uri;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): CardDavAddressBook
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function enableSync(): self
    {
        $this->enabled = true;
        return $this;
    }

    public function disableSync(): self
    {
        $this->enabled = false;
        return $this;
    }

    public function getLastSyncToken(): ?string
    {
        return $this->lastSyncToken;
    }

    public function setLastSyncToken(?string $lastSyncToken): CardDavAddressBook
    {
        $this->lastSyncToken = $lastSyncToken;
        return $this;
    }

    public function getCardDavConfig(): ?CardDavConfig
    {
        return $this->cardDavConfig;
    }

    public function setCardDavConfig(?CardDavConfig $cardDavConfig): CardDavAddressBook
    {
        $this->cardDavConfig = $cardDavConfig;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
