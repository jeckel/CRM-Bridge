<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

namespace App\Component\Contact\Application\Port;

use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Component\Contact\Domain\Entity\Company;
use App\Component\Contact\Domain\Entity\Contact;
use App\Component\Shared\Identity\CardDavAddressBookId;

interface RepositoryPort
{
    public function findByVCardUri(string $vCardUri): ?Contact;

    public function delete(Contact $entity): void;

    public function flush(): void;

    public function persist(Contact $contact): void;

    public function getAddressBookReference(CardDavAddressBookId $addressBookId): CardDavAddressBook;

    public function findCompanyBySlug(string $companySlug): ?Company;
}
