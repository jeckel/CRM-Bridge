<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Calendly;

use App\Entity\IncomingWebhook;
use App\Repository\IncomingWebhookRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class WebhookHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly IncomingWebhookRepository $incomingWebhookRepository
    ) {}

    public function __invoke(Webhook $webhook): void
    {
        $this->incomingWebhookRepository->persist(
            (new IncomingWebhook())
                ->setApplication('Calendly')
                ->setCreatedAt($webhook->createdAt)
                ->setEvent($webhook->event->value)
                ->setPayload($webhook->payload)
        );
    }
}
