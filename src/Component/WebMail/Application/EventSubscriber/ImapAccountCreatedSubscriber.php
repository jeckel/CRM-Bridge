<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:33
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\EventSubscriber;

use App\Component\WebMail\Application\Command\SyncImapAccount;
use App\Component\WebMail\Application\Event\ImapAccountCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ImapAccountCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(private MessageBusInterface $messageBus) {}

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [ImapAccountCreated::class => 'onImapAccountCreated'];
    }

    public function onImapAccountCreated(ImapAccountCreated $event): void
    {
        $this->messageBus->dispatch(new SyncImapAccount($event->accountId));
    }
}
