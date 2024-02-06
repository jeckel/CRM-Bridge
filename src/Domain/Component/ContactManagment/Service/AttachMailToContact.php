<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Domain\Component\ContactManagment\Service;

use App\Domain\Component\ContactManagment\Port\ContactRepository;
use App\Identity\MailId;
use App\ValueObject\Email;
use DateTimeImmutable;

readonly class AttachMailToContact
{
    public function __construct(
        private ContactRepository $contactRepository
    ) {}

    public function __invoke(Email $email, MailId $mailId, DateTimeImmutable $sendAt): void
    {
        $contact = $this->contactRepository->findByEmail($email->getEmail());
        if (null === $contact) {
            return;
        }
        $contact->addMail($mailId, $sendAt);
        $this->contactRepository->save($contact);
    }
}
