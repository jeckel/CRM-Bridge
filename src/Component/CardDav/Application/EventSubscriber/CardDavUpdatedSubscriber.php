<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\EventSubscriber;

use App\Component\CardDav\Application\Command\SyncCardDavAddressBook;
use App\Component\CardDav\Application\Event\CardDavAddressBookUpdated;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CardDavUpdatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {}

    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            CardDavAddressBookUpdated::class => 'onCardDavUpdated',
        ];
    }

    public function onCardDavUpdated(CardDavAddressBookUpdated $event): void
    {
        $this->messageBus->dispatch(new SyncCardDavAddressBook($event->addressBookId));
    }
}
