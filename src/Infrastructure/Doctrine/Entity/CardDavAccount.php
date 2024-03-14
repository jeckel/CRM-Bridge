<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\Identity\CardDavAccountId;
use App\Infrastructure\Doctrine\Repository\CardDavConfigRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use Stringable;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
#[ORM\Entity(repositoryClass: CardDavConfigRepository::class)]
class CardDavAccount implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'card_dav_account_id', type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(length: 180, nullable: false)]
    private string $name;

    #[ORM\Column(length: 255, nullable: false)]
    private string $uri;

    #[ORM\Column(length: 255, nullable: false)]
    private string $login;

    #[ORM\Column(length: 255, nullable: false)]
    private string $password;

    /**
     * @var Collection<string, CardDavAddressBook> $addressBooks
     */
    #[ORM\OneToMany(
        mappedBy: 'cardDavAccount',
        targetEntity: CardDavAddressBook::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $addressBooks;

    private CardDavAccountId $cardDavAccountId;

    public function __construct()
    {
        $this->addressBooks = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getIdentity(): CardDavAccountId
    {
        if (! isset($this->cardDavAccountId)) {
            $this->cardDavAccountId = CardDavAccountId::from($this->id->toString());
        }
        return $this->cardDavAccountId;
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

    public function setName(string $name): CardDavAccount
    {
        $this->name = $name;
        return $this;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return Collection<string, CardDavAddressBook>
     */
    public function getAddressBooks(): Collection
    {
        return $this->addressBooks;
    }

    public function addAddressBook(CardDavAddressBook $addressBook): static
    {
        if (!$this->addressBooks->contains($addressBook)) {
            $this->addressBooks->add($addressBook);
            $addressBook->setCardDavAccount($this);
        }

        return $this;
    }

    public function removeAddressBook(CardDavAddressBook $addressBook): static
    {
        if ($this->addressBooks->removeElement($addressBook)) {
            // set the owning side to null (unless already changed)
            if ($addressBook->getCardDavAccount() === $this) {
                $addressBook->setCardDavAccount(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
