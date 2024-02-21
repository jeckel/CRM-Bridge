<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\EventSubscriber;

use App\Component\ContactManagment\Domain\Service\AppointmentService;
use App\Component\ContactManagment\Domain\Service\ContactProvider;
use App\Component\Shared\Event\AppointmentCreated;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class AppointmentCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ContactProvider $contactProvider,
        private AppointmentService $appointmentService
    ) {}

    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
//            AppointmentCreated::class => 'onAppointmentCreated',
        ];
    }

    public function onAppointmentCreated(AppointmentCreated $event): void
    {
        $contact = $this->contactProvider->findOrCreate(
            firstName: null,
            lastName: null,
            displayName: $event->attendeeName,
            email: $event->attendeeEmail,
            phoneNumber: null
        );

        $this->appointmentService->addAppointmentRequest(
            $contact,
            $event->appointmentDate,
            $event->requestDate,
            $event->appointmentSubject
        );
    }
}
