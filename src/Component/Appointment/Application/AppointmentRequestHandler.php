<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/02/2024
 */

declare(strict_types=1);

namespace App\Component\Appointment\Application;

use App\Component\Shared\Event\AppointmentCreated;
use App\Presentation\Async\Message\AppointmentRequest;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class AppointmentRequestHandler
{
    public function __construct(private EventDispatcherInterface $eventDispatcher) {}

    public function __invoke(AppointmentRequest $webhook): void
    {
        // TODO: Persist Appointment request
        foreach ($webhook->attendees as $attendee) {
            $this->eventDispatcher->dispatch(new AppointmentCreated(
                attendeeName: $attendee->displayName,
                attendeeEmail: $attendee->email,
                accountId: $webhook->accountId,
                requestDate: $webhook->requestDate,
                appointmentDate: $webhook->appointmentDate,
                appointmentSubject: $webhook->appointmentSubject
            ));
        }
    }
}
