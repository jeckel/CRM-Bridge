<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Service;

use App\Component\ContactManagment\Domain\Port\ContactRepository;
use App\Component\Shared\Identity\ImapMailId;
use App\Component\Shared\ValueObject\Email;
use DateTimeImmutable;

readonly class AttachMailToContact
{
    public function __construct(
        private ContactRepository $contactRepository
    ) {}

    public function __invoke(Email $email, ImapMailId $mailId, DateTimeImmutable $sendAt): void
    {
        $contact = $this->contactRepository->findByEmail($email);
        if (null === $contact) {
            return;
        }
        $contact->addMail($mailId, $sendAt);
        $this->contactRepository->save($contact);
    }
}
