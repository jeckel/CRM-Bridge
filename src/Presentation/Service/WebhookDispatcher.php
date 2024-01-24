<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 19:17
 */
declare(strict_types=1);

namespace App\Presentation\Service;

use App\Entity\IncomingWebhook;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class WebhookDispatcher
{
    public function __construct(
        private MessageBusInterface $bus
    ) { }

    public function dispatch(IncomingWebhook $webhook): void
    {
        $this->bus->dispatch($webhook);
    }
}
