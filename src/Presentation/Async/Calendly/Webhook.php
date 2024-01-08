<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Calendly;

class Webhook
{
    public function __construct(
        public readonly string $content
    ) {
    }
}
