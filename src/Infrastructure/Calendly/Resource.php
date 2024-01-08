<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Calendly;

readonly class Resource
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        public ?string $avatar_url,
        public string $created_at,
        public string $current_organization,
        public string $email,
        public string $name,
        public string $resource_type,
        public string $scheduling_url,
        public string $slug,
        public string $timezone,
        public string $updated_at,
        public string $uri
    ) {}
}
