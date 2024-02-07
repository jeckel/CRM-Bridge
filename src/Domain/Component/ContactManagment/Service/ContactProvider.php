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
use App\ValueObject\Email;

readonly class ContactProvider
{
    public function __construct(
        private ContactRepository $contactRepository
    ) {}

    public function findOrCreate(
        ?string $firstName,
        ?string $lastName,
        string $displayName,
        string|Email|null $email,
        ?string $phoneNumber
    ): Contact {
        if (is_string($email)) {
            $email = new Email($email);
        }
        if (null !== $email) {
            $contact = $this->contactRepository->findByEmail($email);
            if ($contact !== null) {
                return $contact;
            }
        }
        // @todo Search also by display name

        // @todo search for contact in EspoCRM

        $contact = new Contact(
            id: ContactId::new(),
            displayName: $displayName,
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            phoneNumber: $phoneNumber
        );
        $this->contactRepository->save($contact);
        return $contact;
    }
}
