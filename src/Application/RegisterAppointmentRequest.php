<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Application;

use App\Domain\Component\ContactManagment\Service\AppointmentService;
use App\Domain\Component\ContactManagment\Service\ContactProvider;
use DateTimeImmutable;

readonly class RegisterAppointmentRequest
{
    public function __construct(
        private ContactProvider $contactProvider,
        private AppointmentService $appointmentService
    ) {}

    public function registerAppointment(
        string $firstName,
        string $lastName,
        string $displayName,
        string $email,
        string $phoneNumber,
        DateTimeImmutable $appointmentDate,
        DateTimeImmutable $requestDate,
        string $appointmentSubject
    ): void {
        $contact = $this->contactProvider->findOrCreate($firstName, $lastName, $displayName, $email, $phoneNumber);
        $this->appointmentService->addAppointmentRequest($contact, $appointmentDate, $requestDate, $appointmentSubject);
    }
}
