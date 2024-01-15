<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async;

use App\Entity\IncomingWebhook;
use App\Repository\IncomingWebhookRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class WebHookHandler
{
    public function __construct(
        private readonly IncomingWebhookRepository $incomingWebhookRepository
    ) {}

    public function __invoke(WebHook $webhook): void
    {
        $this->incomingWebhookRepository->persist(
            (new IncomingWebhook())
                ->setSource('Calendly')
                ->setCreatedAt($webhook->createdAt)
                ->setEvent((string) $webhook->event)
                ->setPayload($webhook->payload)
        );
    }
}
