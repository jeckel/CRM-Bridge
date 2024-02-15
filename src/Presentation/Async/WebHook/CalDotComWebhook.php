<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\WebHook;

use App\Component\Shared\ValueObject\CalDotCom\TriggerEvent;
use App\Component\Shared\ValueObject\WebHookSource;
use DateTimeImmutable;

readonly class CalDotComWebhook implements WebhookInterface
{
    /** @phpstan-ignore-next-line */
    public function __construct(
        public DateTimeImmutable $createdAt,
        public WebHookSource $source,
        public TriggerEvent $event,
        public array $payload
    ) {}
}
