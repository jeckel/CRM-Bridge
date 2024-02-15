<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Service;

use App\Component\ContactManagment\Domain\Entity\Contact;
use App\Component\ContactManagment\Domain\Port\ContactRepository;
use App\Component\Shared\Event\ContactCreated;
use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;
use JeckelLab\Contract\Infrastructure\System\Clock;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class ContactProvider
{
    public function __construct(
        private ContactRepository $contactRepository,
        private EventDispatcherInterface $eventDispatcher,
        private Clock $clock
    ) {}

    public function findOrCreate(
        ?string $firstName,
        ?string $lastName,
        string $displayName,
        string|Email|null $email,
        ?string $phoneNumber,
        ?string $company = null
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
            phoneNumber: $phoneNumber,
            company: $company
        );
        $this->contactRepository->save($contact);

        $this->eventDispatcher->dispatch(
            new ContactCreated(
                $contact->id,
                $contact->email,
                $this->clock->now()
            )
        );
        return $contact;
    }
}