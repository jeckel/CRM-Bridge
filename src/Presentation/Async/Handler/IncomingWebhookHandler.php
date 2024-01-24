<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 19:42
 */
declare(strict_types=1);

namespace App\Presentation\Async\Handler;

use App\Entity\IncomingWebhook;
use App\Presentation\Async\WebHook\CalDotComWebhook;
use App\ValueObject\CalDotCom\TriggerEvent;
use App\ValueObject\WebHookSource;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class IncomingWebhookHandler
{
    public function __construct(private CalDotComHandler $calDotcomHandler)
    {
    }

    public function __invoke(IncomingWebhook $webhook): void
    {
        $source = WebHookSource::tryFrom($webhook->getSource());
        $event = TriggerEvent::tryFrom($webhook->getEvent());
        if ($source === WebHookSource::CAL_DOT_COM && $event !== null) {
            call_user_func(
                $this->calDotcomHandler,
                new CalDotComWebhook(
                    createdAt: $webhook->getCreatedAt(),
                    source: $source,
                    event: $event,
                    payload: $webhook->getPayload()
                )
            );
        }
    }
}
