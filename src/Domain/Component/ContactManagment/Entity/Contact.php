<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 20:09
 */
declare(strict_types=1);

namespace App\Domain\Component\ContactManagment\Entity;

use App\Identity\ContactId;
use DateTimeImmutable;

readonly class Contact
{
    public ContactActivityCollection $activities;

    public function __construct(
        public ContactId $id,
        public ?string $firstName,
        public ?string $lastName,
        public string $displayName,
        public string $email,
        public ?string $phoneNumber,
        public ?string $espoContactId = null,
        ?ContactActivityCollection $activities = null,
    ) {
        $this->activities = $activities ?? new ContactActivityCollection();
    }

    public function addAppointmentRequest(
        DateTimeImmutable $appointmentDate,
        DateTimeImmutable $requestDate,
        string $appointmentSubject
    ): self {
        $this->activities->add(ContactActivity::new(
            date: $requestDate,
            subject: 'Demande de rendez-vous',
            description: sprintf(
                "Date du rendez-vous: %s\nSujet: %s",
                $appointmentDate->format("d/m/Y"),
                $appointmentSubject
            )
        ));
        return $this;
    }
}
