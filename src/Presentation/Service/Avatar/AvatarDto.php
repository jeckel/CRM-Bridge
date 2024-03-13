<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 10:06
 */
declare(strict_types=1);

namespace App\Presentation\Service\Avatar;

readonly class AvatarDto
{
    public function __construct(
        public string $url,
        public string $mimeType
    ) {}
}
