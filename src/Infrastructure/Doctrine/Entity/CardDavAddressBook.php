<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\Identity\AddressBookId;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use Stringable;

#[ORM\Entity(repositoryClass: CardDavAddressBookRepository::class)]
class CardDavAddressBook implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'card_dav_address_book_id', type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: Types::STRING, length: 180, nullable: false)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $uri;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $enabled = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $lastSyncToken = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDefault = false;

    #[ORM\ManyToOne(
        inversedBy: 'addressBooks'
    )]
    #[ORM\JoinColumn(
        name: 'card_dav_account_id',
        referencedColumnName: 'card_dav_account_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private ?CardDavAccount $cardDavAccount = null;

    private AddressBookId $addressBookId;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getIdentity(): AddressBookId
    {
        if (! isset($this->addressBookId)) {
            $this->addressBookId = AddressBookId::from($this->id->toString());
        }
        return $this->addressBookId;
    }

    public function setId(UuidInterface $id): self
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

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function getCardAccount(): ?CardDavAccount
    {
        return $this->cardDavAccount;
    }

    public function setCardDavAccount(?CardDavAccount $cardDavAccount): CardDavAddressBook
    {
        $this->cardDavAccount = $cardDavAccount;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
