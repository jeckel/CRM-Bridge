<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Calendly;

use DateTimeImmutable;

readonly class Webhook
{
    /** @phpstan-ignore-next-line */
    public function __construct(
        public DateTimeImmutable $createdAt,
        public CalendlyEventType $event,
        public array $payload
    ) {}
}
