<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EntityModel;

use App\Component\Shared\Identity\CardDavAddressBookId;
use App\Infrastructure\Doctrine\Exception\RelationNotFoundException;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use Stringable;

class CardDavAddressBook implements Stringable
{
    private CardDavAddressBookId $id;

    private string $name;
    private string $uri;
    private bool $enabled = false;
    private ?string $lastSyncToken = null;
    private CardDavAccount $account;

    public static function new(string $name, string $uri, CardDavAccount $account): self
    {
        $addressBook = new self();
        $addressBook->id = CardDavAddressBookId::new();
        $addressBook->name = $name;
        $addressBook->uri = $uri;
        $addressBook->account = $account;
        return $addressBook;
    }

    public function getId(): CardDavAddressBookId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUri(): string
    {
        return $this->uri;
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

    public function getLastSyncToken(): ?string
    {
        return $this->lastSyncToken;
    }

    public function setLastSyncToken(string $lastSyncToken): CardDavAddressBook
    {
        $this->lastSyncToken = $lastSyncToken;
        return $this;
    }

    public function getCardDavAccount(): CardDavAccount
    {
        return $this->account;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
