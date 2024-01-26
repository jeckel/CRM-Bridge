<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Domain\Component\ContactManagment\Service;

use App\Domain\Component\ContactManagment\Entity\Contact;
use App\Domain\Component\ContactManagment\Port\ContactRepository;
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
