<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\EventSubscriber;

use App\Component\Contact\Domain\Event\ContactEmailAdded;
use App\Component\DirectCommunicationHub\Domain\Service\MailAuthorLinkManager;
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
            ContactEmailAdded::class => 'onContactCreated',
        ];
    }

    public function onContactCreated(ContactEmailAdded $event): void
    {
        $this->attachMailsToAuthor->linkToAuthor($event->emailAddress);
    }
}
