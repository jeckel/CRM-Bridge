<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Webhook;

use App\Presentation\Async\Calendly\CalendlyEventType;
use App\Presentation\Async\Calendly\Webhook;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class Calendly extends AbstractController
{
    #[Route(
        path: '/webhook/calendly',
        methods: ['GET', 'POST']
    )]
    public function __invoke(Request $request, MessageBusInterface $bus): Response
    {
        $content = $request->toArray();
        $bus->dispatch(new Webhook(
            createdAt: new DateTimeImmutable($content['created_at']),
            event: CalendlyEventType::from($content['event']),
            payload: $content['payload']
        ));
        return new Response('200 OK');
    }
}
