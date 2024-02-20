<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\EventSubscriber;

use App\Component\ContactManagment\Domain\Service\AttachMailToContact;
use App\Component\Shared\Event\NewIncomingEmail;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class NewMailSubscriber implements EventSubscriberInterface
{
    public function __construct(private AttachMailToContact $mailAttacher) {}

    #[Override]
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
