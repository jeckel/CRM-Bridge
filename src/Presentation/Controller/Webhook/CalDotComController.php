<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Webhook;

use App\Presentation\Async\WebHook\CalDotComWebhook;
use App\Presentation\Service\WebhookDispatcher;
use App\ValueObject\CalDotCom\TriggerEvent;
use App\ValueObject\WebHookSource;
use DateTimeImmutable;
use JeckelLab\Contract\Infrastructure\System\Clock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalDotComController extends AbstractWebhookController
{
    #[Route(
        path: '/webhook/cal-dot-com',
        methods: ['GET', 'POST']
    )]
    public function __invoke(Request $request, WebhookDispatcher $webhookDispatcher, Clock $clock): Response
    {
        $content = $request->toArray();
        $source = WebHookSource::CAL_DOT_COM;
        $createdAt = isset($content['createdAt']) ? new DateTimeImmutable($content['createdAt']) : $clock->now();
        $event = TriggerEvent::tryFrom($content['triggerEvent']);

        $webhook = $this->persistWebhook(
            source: $source,
            createdAt: $createdAt,
            event: $event->value ?? $content['triggerEvent'],
            content: $content
        );

        if ($event !== null) {
            $webhookDispatcher->dispatch($webhook);
        }
        return new Response('200 OK');
    }
}
