<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Handler;

use App\Application\RegisterAppointmentRequest;
use App\Presentation\Async\WebHook\CalDotComWebhook;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CalDotComHandler
{
    public function __construct(
        private RegisterAppointmentRequest $registerAppointmentRequest
    ) {}

    public function __invoke(CalDotComWebhook $webhook): void
    {
        foreach ($webhook->payload['payload']['attendees'] as $attendee) {
            $this->registerAppointmentRequest->registerAppointment(
                firstName: '',
                lastName: '',
                displayName: $attendee['name'],
                email: $attendee['email'],
                phoneNumber: '',
                appointmentDate: new DateTimeImmutable(),
                requestDate: new DateTimeImmutable(),
                appointmentSubject: 'Appointment',
            );
        }
    }
}
