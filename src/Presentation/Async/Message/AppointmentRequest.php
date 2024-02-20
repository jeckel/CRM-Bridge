<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Message;

use App\Component\Shared\Identity\AccountId;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\IncomingWebhook;
use App\Presentation\Async\Message\AppointmentRequest\Attendee;
use DateTimeImmutable;

final readonly class AppointmentRequest
{
    /**
     * @param Attendee[] $attendees
     */
    public function __construct(
        public AccountId $accountId,
        public DateTimeImmutable $appointmentDate,
        public DateTimeImmutable $requestDate,
        public string $appointmentSubject,
        public array $attendees
    ) {}

    public static function fromCalDotComWebhook(IncomingWebhook $webhook): self
    {
        /** @var array{payload: array{title: string, startTime: string, attendees: array{name?: string, email?: string}[]}} $payload */
        $payload = $webhook->getPayload();
        $attendees = [];
        foreach ($payload['payload']['attendees'] as $attendee) {
            if (! isset($attendee['email'], $attendee['name'])) {
                continue;
            }
            $attendees[] = new Attendee(
                firstName: null,
                lastName: null,
                displayName: $attendee['name'],
                email: new Email($attendee['email']),
                phoneNumber: null
            );
        }
        return new self(
            accountId: $webhook->getAccountId(),
            appointmentDate: new DateTimeImmutable($payload['payload']['startTime']),
            requestDate: $webhook->getCreatedAt(),
            appointmentSubject: $payload['payload']['title'],
            attendees: $attendees,
        );
    }
}
