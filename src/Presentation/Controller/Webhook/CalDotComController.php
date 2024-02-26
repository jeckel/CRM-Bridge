<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Webhook;

use App\Component\Shared\ValueObject\CalDotCom\TriggerEvent;
use App\Component\Shared\ValueObject\WebHookSource;
use App\Presentation\Service\WebHookMessageFactory;
use DateTimeImmutable;
use JeckelLab\Contract\Infrastructure\System\Clock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CalDotComController extends AbstractWebhookController
{
    #[Route(
        path: '/webhook/cal-dot-com',
        methods: ['GET', 'POST'],
        format: 'json'
    )]
    #[IsGranted('ROLE_CAL_DOT_COM', statusCode: 403, exceptionCode: 10010)]
    public function __invoke(
        Request $request,
        //        MessageBusInterface $messageBus,
        //        WebHookMessageFactory $messageFactory,
        Clock $clock
    ): Response {
        $content = $request->toArray();
        $source = WebHookSource::CAL_DOT_COM;
        $createdAt = isset($content['createdAt']) ? new DateTimeImmutable($content['createdAt']) : $clock->now();
        $event = TriggerEvent::tryFrom($content['triggerEvent']);

        //        $webhook = $this->persistWebhook(
        $this->persistWebhook(
            source: $source,
            createdAt: $createdAt,
            event: $event->value ?? $content['triggerEvent'],
            content: $content
        );
        //        if ($event !== null) {
        //            $messageBus->dispatch(
        //                $messageFactory->from($webhook)
        //            );
        //        }
        return $this->json(['status' => 'ok']);
    }
}
