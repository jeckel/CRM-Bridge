<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\EventSubscriber;

use App\Component\DirectCommunicationHub\Domain\Service\MailAuthorLinkManager;
use App\Component\Shared\Event\ContactCreated;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class ContactCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailAuthorLinkManager $attachMailsToAuthor
    ) {}

    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            ContactCreated::class => 'onContactCreated',
        ];
    }

    public function onContactCreated(ContactCreated $event): void
    {
        if (null === $event->email) {
            return;
        }
        $this->attachMailsToAuthor->linkToAuthor($event->email);
    }
}
