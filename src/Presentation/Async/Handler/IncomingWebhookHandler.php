<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 19:42
 */
declare(strict_types=1);

namespace App\Presentation\Async\Handler;

use App\Infrastructure\Doctrine\Entity\IncomingWebhook;
use App\Presentation\Async\WebHook\CalDotComWebhook;
use App\ValueObject\CalDotCom\TriggerEvent;
use App\ValueObject\WebHookSource;
use JeckelLab\Contract\Infrastructure\System\Clock;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class IncomingWebhookHandler
{
    public function __construct(private CalDotComHandler $calDotcomHandler, private Clock $clock) {}

    public function __invoke(IncomingWebhook $webhook): void
    {
        $source = WebHookSource::tryFrom($webhook->getSource());
        $event = TriggerEvent::tryFrom((string) $webhook->getEvent());
        if ($source === WebHookSource::CAL_DOT_COM && $event !== null) {
            call_user_func(
                $this->calDotcomHandler,
                new CalDotComWebhook(
                    createdAt: $webhook->getCreatedAt() ?? $this->clock->now(),
                    source: $source,
                    event: $event,
                    payload: $webhook->getPayload() ?? []
                )
            );
        }
    }
}
