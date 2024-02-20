<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Service;

use App\Component\Shared\ValueObject\CalDotCom\TriggerEvent;
use App\Component\Shared\ValueObject\Email;
use App\Component\Shared\ValueObject\WebHookSource;
use App\Infrastructure\Doctrine\Entity\IncomingWebhook;
use App\Presentation\Async\Message\AppointmentRequest;
use App\Presentation\Async\Message\AppointmentRequest\Attendee;
use App\Presentation\Async\Message\Message;
use DateTimeImmutable;
use RuntimeException;

readonly class WebHookMessageFactory
{
    public function from(IncomingWebhook $webhook): Message
    {
        if ($webhook->getSource() === WebHookSource::CAL_DOT_COM->value && $webhook->getEvent() === TriggerEvent::CREATED->value) {
            return $this->fromCalDotComWebhook($webhook);
        }
        throw new RuntimeException('Unknown webhook');
    }

    public function fromCalDotComWebhook(IncomingWebhook $webhook): AppointmentRequest
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
        return new AppointmentRequest(
            accountId: $webhook->getAccountId(),
            appointmentDate: new DateTimeImmutable($payload['payload']['startTime']),
            requestDate: $webhook->getCreatedAt(),
            appointmentSubject: $payload['payload']['title'],
            attendees: $attendees,
        );
    }
}
