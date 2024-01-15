<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Webhook;

use App\Presentation\Async\WebHook\CalDotComWebHook;
use App\ValueObject\WebHookSource;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class CalDotComController extends AbstractController
{
    #[Route(
        path: '/webhook/cal-dot-com',
        methods: ['GET', 'POST']
    )]
    public function __invoke(Request $request, MessageBusInterface $bus): Response
    {
        $content = $request->toArray();
        $bus->dispatch(new CalDotComWebHook(
            createdAt: new DateTimeImmutable($content['createdAt'] ?? "now"),
            source: WebHookSource::CAL_DOT_COM,
            event: $content['triggerEvent'] ?? 'Unknown event',
            payload: $content
        ));
        return new Response('200 OK');
    }
}
