<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Handler;

use App\Entity\Contact;
use App\Event\ContactCreated;
use App\Identity\ContactId;
use App\Presentation\Async\WebHook\CalDotComWebhook;
use App\Repository\ContactRepository;
use DateTimeImmutable;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CalDotComHandler
{
    public function __construct(
        private ContactRepository $contactRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function __invoke(CalDotComWebhook $webhook): void
    {
        foreach ($webhook->payload['attendees'] as $attendee) {
            $contact = $this->contactRepository->findOneBy(['email' => $attendee['email']]);
            if ($contact === null) {
                $contact = new Contact();
                $contact->setEmail($attendee['email'])
                    ->setDisplayName($attendee['name']);
                $this->contactRepository->persist($contact);
            }
            $this->eventDispatcher->dispatch(
                new ContactCreated(
                    ContactId::from((string) $contact->getId()),
                    new DateTimeImmutable()
                )
            );
        }
    }
}
