<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async;

use App\ValueObject\WebHookSource;
use DateTimeImmutable;
use Stringable;

readonly class WebHook
{
    /** @phpstan-ignore-next-line */
    public function __construct(
        public DateTimeImmutable $createdAt,
        public WebHookSource $source,
        public string|Stringable $event,
        public array $payload
    ) {}
}
