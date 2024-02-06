<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Domain\Component\ContactManagment\Service;

use App\Domain\Component\ContactManagment\Entity\Contact;
use App\Domain\Component\ContactManagment\Port\ContactRepository;
use App\Domain\Component\ContactManagment\Port\VCard;
use JeckelLab\Contract\Infrastructure\System\Clock;

readonly class ContactVCardUpdater
{
    public function __construct(
        private ContactRepository $repository,
        private Clock $clock
    ) {}

    public function sync(VCard $vCard): void
    {
        $contact = $this->repository->findByVCard($vCard->vCardUri());
        if (null === $contact && ($email = $vCard->email()) !== null) {
            $contact = $this->repository->findByEmail($email);
        }
        if (null === $contact) {
            $contact = Contact::new($vCard->displayName());
        }
        $contact->updateFromVCard($vCard, $this->clock->now());
        $this->repository->save($contact);
    }
}
