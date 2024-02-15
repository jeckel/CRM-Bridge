<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Service;

use App\Component\ContactManagment\Domain\Entity\Contact;
use App\Component\ContactManagment\Domain\Port\ContactRepository;
use App\Component\ContactManagment\Domain\Port\VCard;
use App\Component\Shared\Event\ContactCreated;
use App\Component\Shared\Identity\AccountId;
use App\Component\Shared\Identity\AddressBookId;
use JeckelLab\Contract\Infrastructure\System\Clock;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class ContactVCardUpdater
{
    public function __construct(
        private ContactRepository $repository,
        private EventDispatcherInterface $eventDispatcher,
        private Clock $clock
    ) {}

    public function sync(VCard $vCard, AddressBookId $addressBookId): void
    {
        $newContact = false;
        $contact = $this->repository->findByVCard($vCard->vCardUri());
        if (null === $contact && ($email = $vCard->email()) !== null) {
            $contact = $this->repository->findByEmail($email);
        }
        if (null === $contact) {
            $contact = Contact::new($vCard->displayName());
            $newContact = true;
        }
        $contact->updateFromVCard($vCard, $this->clock->now(), $addressBookId);
        $this->repository->save($contact);
        if ($newContact) {
            $this->eventDispatcher->dispatch(
                new ContactCreated(
                    $contact->id,
                    $contact->email,
                    $this->clock->now()
                )
            );
        }
    }
}
