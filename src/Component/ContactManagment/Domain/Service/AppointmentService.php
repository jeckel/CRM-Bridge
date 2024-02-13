<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Service;

use App\Component\ContactManagment\Domain\Entity\Contact;
use App\Component\ContactManagment\Domain\Port\ContactRepository;
use DateTimeImmutable;

readonly class AppointmentService
{
    public function __construct(
        private ContactRepository $contactRepository
    ) {}

    public function addAppointmentRequest(
        Contact $contact,
        DateTimeImmutable $appointmentDate,
        DateTimeImmutable $requestDate,
        string $appointmentSubject
    ): void {
        $contact->addAppointmentRequest(
            appointmentDate: $appointmentDate,
            requestDate: $requestDate,
            appointmentSubject: $appointmentSubject
        );
        $this->contactRepository->save($contact);
    }
}
