<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EntityModel;

use App\Component\Shared\Identity\CardDavAccountId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Stringable;

class CardDavAccount implements Stringable
{
    /**
     * @var Collection<string, CardDavAddressBook> $addressBooks
     */
    private Collection $addressBooks;

    public function __construct(
        private CardDavAccountId $id,
        private string $name,
        private string $uri,
        private string $login,
        private string $password
    ) {
        $this->addressBooks = new ArrayCollection();
    }

    public function getId(): CardDavAccountId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function login(): string
    {
        return $this->login;
    }

    public function password(): string
    {
        return $this->password;
    }


    /**
     * @return Collection<string, CardDavAddressBook>
     */
    public function getAddressBooks(): Collection
    {
        return $this->addressBooks;
    }
//
//    public function addAddressBook(CardDavAddressBook $addressBook): static
//    {
//        if (!$this->addressBooks->contains($addressBook)) {
//            $this->addressBooks->add($addressBook);
//            $addressBook->setCardDavAccount($this);
//        }
//
//        return $this;
//    }
//
//    public function removeAddressBook(CardDavAddressBook $addressBook): static
//    {
//        if ($this->addressBooks->removeElement($addressBook)) {
//            // set the owning side to null (unless already changed)
//            if ($addressBook->getCardDavAccount() === $this) {
//                $addressBook->setCardDavAccount(null);
//            }
//        }
//
//        return $this;
//    }

    public function __toString()
    {
        return $this->name;
    }
}
