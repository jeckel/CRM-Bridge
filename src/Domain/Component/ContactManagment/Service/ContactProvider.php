<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Domain\Component\ContactManagment\Service;

use App\Domain\Component\ContactManagment\Entity\Contact;
use App\Domain\Component\ContactManagment\Port\ContactRepository;
use App\Identity\ContactId;

readonly class ContactProvider
{
    public function __construct(
        private ContactRepository $contactRepository
    ) {}

    public function findOrCreate(
        string $firstName,
        string $lastName,
        string $displayName,
        string $email,
        string $phoneNumber
    ): Contact {
        $contact = $this->contactRepository->findByEmail($email);
        if ($contact !== null) {
            return $contact;
        }

        // @todo search for contact in EspoCRM

        $contact = new Contact(
            ContactId::new(),
            $firstName,
            $lastName,
            $displayName,
            $email,
            $phoneNumber
        );
        $this->contactRepository->save($contact);
        return $contact;
    }
}
