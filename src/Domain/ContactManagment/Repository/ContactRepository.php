<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 20:08
 */
declare(strict_types=1);

namespace App\Domain\ContactManagment\Repository;

use App\Domain\ContactManagment\Entity\Contact;
use App\Identity\ContactId;

interface ContactRepository
{
    public function save(Contact $contact): void;

    public function getById(ContactId $contactId): Contact;

    public function findByEmail(string $email): ?Contact;
}
