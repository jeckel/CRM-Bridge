<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Service;

use App\Component\ContactManagment\Application\Dto\ContactDto;
use App\Component\ContactManagment\Domain\Entity\Contact;
use App\Component\ContactManagment\Domain\Port\ContactRepository;
use App\Component\Shared\Identity\ContactId;

readonly class UpsertContactManager
{
    public function __construct(
        private ContactRepository $contactRepository,
    ) {}

    public function upsertContact(
        ContactDto $data,
        ?ContactId $contactId = null,
        Contact $contact = null
    ): Contact {
        if ($contact === null && $contactId !== null) {
            $contact = $this->contactRepository->getById($contactId);
        }
        if ($contact === null && $data->emailAddress !== null) {
            $contact = $this->contactRepository->findByEmail($data->emailAddress);
        }
        $contact = (null !== $contact) ?
            // Update contact
            $contact->update(
                displayName: $data->displayName,
                firstName: $data->firstName,
                lastName: $data->lastName,
                emailAddress: $data->emailAddress,
                phoneNumber: $data->phoneNumber,
                company: $data->company
            ) :
            // Create new contact
            $this->createContact($data);

        $this->contactRepository->save($contact);
        return $contact;
    }

    protected function createContact(ContactDto $data): Contact
    {
        return Contact::create(
            displayName: $data->displayName,
            firstName: $data->firstName,
            lastName: $data->lastName,
            emailAddress: $data->emailAddress,
            phoneNumber: $data->phoneNumber,
            company: $data->company
        );
    }
}
