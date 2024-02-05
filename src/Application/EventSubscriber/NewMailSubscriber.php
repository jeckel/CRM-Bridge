<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Application\EventSubscriber;

use App\Domain\Component\ContactManagment\Service\AttachMailToContact;
use App\Domain\Component\ContactManagment\Service\ContactProvider;
use App\Event\NewIncomingEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class NewMailSubscriber implements EventSubscriberInterface
{
    public function __construct(private AttachMailToContact $mailAttacher) {}

    public static function getSubscribedEvents(): array
    {
        return [
            NewIncomingEmail::class => 'onNewIncomingEmail',
        ];
    }

    public function onNewIncomingEmail(NewIncomingEmail $event): void
    {
        $this->mailAttacher->__invoke($event->email, $event->mailId, $event->sendAt);
    }
}
