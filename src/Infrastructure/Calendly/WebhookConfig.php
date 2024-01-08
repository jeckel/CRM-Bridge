<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Calendly;

readonly class WebhookConfig
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @param string[] $events
     */
    public function __construct(
        public string $callback_url,
        public string $created_at,
        public string $creator,
        public array $events,
        public string $organization,
        public ?string $retry_started_at,
        public string $scope,
        public string $state,
        public string $updated_at,
        public string $uri,
        public ?string $user
    ) {}

    public function getUid(): string
    {
        $pos = strrpos($this->uri, '/');
        if ($pos === false) {
            $pos = 0;
        }
        return substr($this->uri, $pos + 1);
    }
}
