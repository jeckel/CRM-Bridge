<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Webhook;

use App\Component\Shared\ValueObject\WebHookSource;
use DateTimeImmutable;
use JeckelLab\Contract\Infrastructure\System\Clock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalendlyWebhookController extends AbstractWebhookController
{
    #[Route(
        path: '/webhook/calendly',
        methods: ['GET', 'POST']
    )]
    public function __invoke(Request $request, Clock $clock): Response
    {
        $content = $request->toArray();

        $source = WebHookSource::CAL_DOT_COM;
        $createdAt = isset($content['created_at']) ? new DateTimeImmutable($content['created_at']) : $clock->now();

        $this->persistWebhook(
            source: $source,
            createdAt: $createdAt,
            event: $content['event'],
            content: $content
        );
        return new Response('200 OK');
    }
}
