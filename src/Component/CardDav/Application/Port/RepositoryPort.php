<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

namespace App\Component\CardDav\Application\Port;

use App\Component\CardDav\Domain\Entity\CardDavAccount;
use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Component\Shared\Identity\CardDavAccountId;
use App\Component\Shared\Identity\CardDavAddressBookId;

interface RepositoryPort
{
    public function persist(CardDavAccount|CardDavAddressBook $entity): void;

    public function flush(): void;

    public function getAccountById(CardDavAccountId $accountId): CardDavAccount;

    /**
     * @param CardDavAccountId $accountId
     * @return iterable<CardDavAddressBook>
     */
    public function getAddressBooksByAccount(CardDavAccountId $accountId): iterable;

    public function getAddressBookById(CardDavAddressBookId $addressBookId): CardDavAddressBook;

    /**
     * @return iterable<CardDavAddressBook>
     */
    public function getEnabledAddressBooks(): iterable;

    public function findAddressBookByUri(string $addressBookUri, CardDavAccountId $accountId): ?CardDavAddressBook;
}
